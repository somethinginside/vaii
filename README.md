Description
Unicorns World is a web application for an online store specializing in magical products and accessories related to unicorns. The project is implemented using PHP, MySQL, and HTML/CSS/JavaScript without using any frameworks. It includes user registration, authentication, shopping cart, order management, and an admin panel.

How to Run
1. Install [OpenServer](https://ospanel.io/)
2. Place the project in the folder home/unicornsworld.local/public/
3. Start OpenServer and create a virtual host unicornsworld.local
4. Create a database unicorns_world and run the SQL schema (see schema.sql)
5. Launch the site: http://unicornsworld.local

User Roles
- Guest: View catalog and unicorns
- User: Registration, login, add to cart, create orders
- Admin: Full access to admin panel, manage products, unicorns, and orders

Technologies
- PHP 8.1
- MySQL 5.7
- HTML, CSS, JavaScript
- OpenServer (local server)

Features
- User registration and authentication
- Product catalog with filtering and search
- Shopping cart with quantity management and stock limitation
- Order creation with status tracking
- Admin panel for managing products, unicorns, and orders
- Separation of application logic and presentation layer (without using MVC frameworks)
- Responsive design  in a unified style
