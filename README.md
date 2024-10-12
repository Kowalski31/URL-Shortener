# URL-Shortener

## Overview
This project is a URL shortener application built with Laravel (for the backend) and React.js (for the frontend). It allows users to input a long URL and receive a shortened version, which can be shared or accessed easily. The system also provide a redirect service to handle the shortened URLs.

## Features
- Generate shortened URLs for any valid input URL
- Redirect shortened URLs to their original counterparts
- Error handling for invalid URLs
- Basic user interface for inputting URLs and displaying results
- API endpoints to create and resolve shortened URLs

## Setup Instructions

### Prerequisites
Make sure you have the following installed:
- PHP 8.x or higher (XamPP)
- Composer
- Node.js & npm
- MySQL (XamPP)
- Laravel 10.x
- React.js

## Technology Choices

### Backend
- **Laravel**: Chosen for its robust routing, middleware support, and ease of integrating with a database and user authentication. Laravel’s built-in ORM, Eloquent, makes database management simpler. 

### Frontend
- **React.js**: Selected for its component-based architecture and fast rendering of UI. It makes building dynamic and interactive user interfaces easier.

### Database
- **MySQL**: Chosen for its reliability and ease of integration with Laravel. MySQL offers strong support for relational data and is widely used in web applications. It is also integrated in XamPP too.

### Additional Packages
- **Laravel Sanctum**: Used for API developments.

## Completed Features
1. FE
- Implement a form to submit long URLs
- Display the generated short URL after submission
- Include a "Copy to Clipboard" button for the short URL
- Implement basic styling for a clean, responsive design

2. BE
- Create an API endpoint to receive long URLs and return shortened versions
- Implement a redirect service to handle requests for shortened URLs
- Generate unique short codes for each submitted URL
- Allow duplicate long URLs (each submission should create a new short URL)

3. Database
- Store mappings between short codes and original URLs
- Save creation dates for each shortened URL

4. Nice-to-have Features
- Custom Short Codes: Allow users to specify their own custom short codes

## Known Issues / Limitations
- URL expiry is not implemented, which could lead to an infinitely growing database over time.
- Password Protection: Allow users to set a password for accessing certain shortened URLs

## Future Improvements
- **Authentication**: Add user accounts and the ability to manage user-specific URLs.
- **URL Expiration**: Implement automatic expiration for URLs after a set time.
- **Advanced Analytics**: Add detailed analytics, such as geolocation of clicks, referrer tracking, and device type.
