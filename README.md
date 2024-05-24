# Hotel Dashboard

Welcome to Hotel Dashboard, a Symfony project developed by Alshalal/Hiess. This project aims to provide a comprehensive management system for a hotel, facilitating room reservations, cart management, checkout processes, and email notifications. Additionally, it includes a calendar feature for displaying upcoming and current bookings per day. A notable feature is the "Lost and Found" page, where items found by hotel staff in guest rooms are logged for retrieval by their owners.

## Features

- **Room Reservation**: Manage room reservations efficiently.
- **Cart Management**: Allow guests to add, remove, and modify items in their cart.
- **Checkout Process**: Streamline the checkout process for guests.
- **Email Notifications**: Send automated email notifications for booking confirmations, reminders, and receipts.
- **Calendar View**: View upcoming and current bookings per day.
- **Lost and Found Page**: Log and display items found in guest rooms for easy retrieval.

## Installation

1. **Clone the Repository**: `git clone https://github.com/Alshalal/HotelDashboard.git`
2. **Install Dependencies**: `composer install`
3. **Set Up Environment Variables**: Copy `.env.dist` to `.env` and configure your environment variables, including database connection details and email settings.
4. **Set Up Database**: Run `php bin/console doctrine:database:create` to create the database, and then `php bin/console doctrine:migrations:migrate` to apply migrations.
5. **Start the Development Server**: Run `symfony server:start` to start the Symfony development server.

## Usage

1. **Accessing the Dashboard**: Visit the URL provided by the Symfony development server.
2. **Room Reservation**: Navigate to the reservation section to book rooms for guests.
3. **Cart Management**: Guests can add items to their cart and modify quantities as needed.
4. **Checkout Process**: Process guest checkouts, generate receipts, and send confirmation emails.
5. **Calendar View**: View upcoming and current bookings per day to manage hotel occupancy.
6. **Lost and Found Page**: Access the Lost and Found page to see items found in guest rooms. Owners can claim their lost items here.

## Contributing

Thank you for considering contributing to Hotel Dashboard! If you'd like to contribute, please fork the repository and submit a pull request. For major changes, please open an issue first to discuss your ideas.

## License

Hotel Dashboard is open-source software licensed under the [MIT license](LICENSE).

## Contact

For any inquiries or support, please contact the developers:

- **Alshalal**: [casper.king14@gmail.com](mailto:casper.king14@gmail.com)
- **Hiess**: [elias.hiess@koerbler.com](mailto:elias.hiess@koerbler.com)

