<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ExploreController extends Controller
{
    public function index()
    {
        return view('explore');
    }

    public function search(Request $request)
    {
        $query   = $request->get('query', '');
        $topic   = $request->get('topic', '');
        $page    = $request->get('page', 1);
        $yearFrom = $request->get('year_from', '');
        $yearTo   = $request->get('year_to', '');
        $perPage  = 10;

        $searchTerm = $query ?: $topic ?: 'research';

        // Build filter string
        $filters = ['is_oa:true'];

        if ($yearFrom && $yearTo) {
            $filters[] = "publication_year:{$yearFrom}-{$yearTo}";
        } elseif ($yearFrom) {
            $filters[] = "publication_year:>{$yearFrom}";
        } elseif ($yearTo) {
            $filters[] = "publication_year:<{$yearTo}";
        }

        $filterString = implode(',', $filters);
        $cacheKey     = "explore_{$searchTerm}_{$page}_{$yearFrom}_{$yearTo}";

        $data = Cache::remember($cacheKey, 300, function () use ($searchTerm, $page, $perPage, $filterString) {
            $response = Http::timeout(10)->get('https://api.openalex.org/works', [
                'search'   => $searchTerm,
                'page'     => $page,
                'per-page' => $perPage,
                'filter'   => $filterString,
                'select'   => 'id,title,authorships,publication_year,primary_location,abstract_inverted_index,topics,cited_by_count,open_access',
                'sort'     => 'cited_by_count:desc',
            ]);

            if ($response->failed()) return null;

            return $response->json();
        });

        if (!$data) {
            return response()->json(['error' => 'Failed to fetch research papers.'], 500);
        }

        $papers = collect($data['results'] ?? [])->map(function ($work) {
            $abstract = '';
            if (!empty($work['abstract_inverted_index'])) {
                $words = [];
                foreach ($work['abstract_inverted_index'] as $word => $positions) {
                    foreach ($positions as $pos) {
                        $words[$pos] = $word;
                    }
                }
                ksort($words);
                $abstract = implode(' ', $words);
                $abstract = strlen($abstract) > 300
                    ? substr($abstract, 0, 300) . '...'
                    : $abstract;
            }

            $authors = collect($work['authorships'] ?? [])
                ->take(3)
                ->map(fn($a) => $a['author']['display_name'] ?? '')
                ->filter()
                ->join(', ');

            if (count($work['authorships'] ?? []) > 3) {
                $authors .= ' et al.';
            }

            $url = $work['open_access']['oa_url']
                ?? $work['primary_location']['landing_page_url']
                ?? null;

            $topics = collect($work['topics'] ?? [])
                ->take(3)
                ->pluck('display_name')
                ->toArray();

            return [
                'id'       => $work['id'],
                'title'    => $work['title'] ?? 'Untitled',
                'authors'  => $authors ?: 'Unknown Authors',
                'year'     => $work['publication_year'] ?? 'N/A',
                'abstract' => $abstract ?: 'No abstract available.',
                'url'      => $url,
                'topics'   => $topics,
                'cited_by' => $work['cited_by_count'] ?? 0,
                'journal'  => $work['primary_location']['source']['display_name'] ?? null,
                'is_oa'    => $work['open_access']['is_oa'] ?? false,
            ];
        });

        return response()->json([
            'papers' => $papers,
            'meta'   => [
                'total'   => $data['meta']['count']    ?? 0,
                'page'    => $data['meta']['page']     ?? 1,
                'perPage' => $data['meta']['per_page'] ?? $perPage,
            ],
        ]);
    }
}