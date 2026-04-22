<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="#"><img src="https://img.shields.io/badge/Subject-IT9A-red" alt="IT9A Subject"></a>
<a href="#"><img src="https://img.shields.io/badge/API-OpenAlex-blue" alt="OpenAlex API"></a>
<a href="#"><img src="https://img.shields.io/badge/Stack-Laravel_Breeze_%2B_React-4dc71f" alt="Stack"></a>
<a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-yellow.svg" alt="License"></a>
</p>

About Research Management Software
This application is a specialized research discovery and organization platform developed strictly for the IT9A college course. It leverages the power of the OpenAlex API to fetch global academic data while providing a local ecosystem for students and researchers to collaborate.

We have combined the security of Laravel Breeze with the interactivity of React to create a seamless research experience. Key features included in this project:

OpenAlex Integration: Fetch and display high-quality metadata from the OpenAlex global index.

Group Collaboration: Create research groups, invite colleagues, and manage collective projects.

Internal Publishing: A dedicated dashboard to view papers published specifically within the app.

In-App Reader: Seamlessly read PDF research papers using the pdfjs-dist library integration.

File Management: Functionality to add new papers and download them for offline use.

Group Control: Full CRUD operations for group management, including the ability to delete groups and manage invitations.

API Reference
The system provides a localized API for managing internal items and integrating with external data sources.

Get all items
HTTP
GET /api/items?api_key={your_key}
Get specific item
HTTP
GET /api/items/${id}
Utility Functions
For the mathematical and logic requirements of the IT9A curriculum, the system includes native utility functions:

add(num1, num2)
The add function takes two numbers and returns the sum. This is used for internal data calculation and reporting metrics.

Technical Implementation
This project is built using a modern full-stack approach:

Authentication: Scaffolding provided by Laravel Breeze.

Frontend: React components for a dynamic user interface.

PDF Handling: PDF.js for rendering documents directly in the browser.

Data Source: OpenAlex API for academic entity retrieval.

Installation
Clone the repository and enter the directory.

Run composer install and npm install.

Configure your .env file (Database and OpenAlex credentials).

Run php artisan migrate.

Start the development environment with php artisan serve and npm run dev.

IT9A Project Notice
This software is submitted as a requirement for the IT9A subject. It is designed to demonstrate proficiency in backend framework management, API consumption, and frontend state management.

License
This IT9A project is open-sourced software licensed under the MIT license.
