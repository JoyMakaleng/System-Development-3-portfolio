# System-Development-3-portfolio
Assignment and systems development coursework implementing PHP and MySQL

Database Names `warehouse`, `booking system`, `roadwork system`, `beauty parlour`

 Questions
[Question_1_Warehouse] | Warehouse Inventory System | Item cataloging, supplier lookups, CSV export | MySQL via PDO |
[Question_2_Passenger_Booking] | Passenger Booking & Reporting | Reservation form, destination search, fare analytics | MySQL via PDO |
[Question_3_Roadwork_System | Earthmoving Cost Estimator | Dynamic customer select, formula-based rate card, 15% VAT | MySQL via MySQLi |
[Question_4_Beauty_Parlour] | Salon Appointment Portal | Session validation, relational appointment bookings | MySQL via MySQLi |

Question 1: Warehouse Inventory System
`WarehouseForum.php`, `dbconnWarehouse.php`

- Captures inventory items with category tagging (Stationary, Hardware, Electronics, Software)
- Secure insertion into `warehouse_items
- Supplier search query 
- Direct CSV export to Excel

Question 2: Passenger Booking & Reporting System

 `PassengerBooking.php`, `SearchBookings.php`, `ViewBookings.php`
  - Records trip bookings with passenger names, destinations, and fares using PDO prepared statements
  - Search route (SearchBookings.php) with reset controls and error messages when do not records match
  - calculating total bookings, average fare, and highest recorded fare
  - Export CSV
  - 
Question 3: Earthmoving & Roadwork Project Estimator

  - Accesses registered corporate clients from the `customers` database table into a form select dropdown
  - Calculates exact project billing metrics
    - Work Time = Distance\times 4.5
    - $\text{Hourly Fee} = \text{Work Time} \times \text{R}1,250$
    - Equipment overhead (R20,000) and Labour overhead (R1,000)
    - Pre-VAT total and 15% VAT calculation

Question 4: Beauty Parlour Appointment Portal

  - Authenticates users 
  - Records appointments linking treatment notes, stylist assignments, and booking dates
  - Uses foreign key integrity between `clients` and `appointment` tables
