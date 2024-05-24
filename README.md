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

# Team1_Alshalal-Hiess

# Projekttagebuch
## [Hotel Dashboard]
## [Alshalal]
---
Datum|AP NR|Zeitraum|Aufwand|Ort|Tätigkeit|Probleme|Quellen
-----|-----|--------|-------|---|---------|--------|-------
29.04.2024|1.1|08:00-16:25|6h30min|LBS|Ideenfindung|Thema Finden|[Hotel Dashboard](https://dribbble.com/shots/16194078-Tamago-Hotel-Booking-Dashboard-Exploration)
30.04.2024|1.1|7:30-09:00|1h30min|LBS|Projekt Handbuch 0.1|........|[Hotel Dashboard](https://www.eduvidual.at/)
02.05.2024|1.1|9:25-12:50|3h25min|LBS|Projekt Handbuch 0.2|Gantt Chart Vorlage / Arbeitspakete definieren|[Gantt Chart](https://www.onlinegantt.com/#/gantt)
03.05.2024|1.1|10:15-12:50|2h35min|LBS|Projekt Handbuch 0.3|Dokumentation Inhalt / Recherche|[WIKI](https://de.wikipedia.org/wiki/Wikipedia:Hauptseite)
06.05.2024|1.1|10:00-12:20|2h20min|LBS|Projekt Handbuch 0.4|Dokumentation Inhalt Theorie / Install Wsl|[Ubuntu](https://ubuntu.com/server/docs/how-to-install-apache2)
07.05.2024|1.1|07:30-09:00|1h30min|LBS|Projekt Handbuch 0.5|Framework Wählen / Symfony Document install first steps|[Symfony](https://symfony.com/doc/current/setup.html)
10.05.2024|1.1|10:15-12:50|2h35min|LBS|Projekt Handbuch 0.6|Config File einrichten für Login Controller|[Hotel Dashboard](https://www.eduvidual.at/)
13.05.2024|1.1|10:00-12:20|2h20min|LBS|Entwicklung 0.7|Create Auth|[Symfony security](https://symfony.com/doc/current/security.html#create-user-class)
14.05.2024|1.1|10:00-12:20|2h20min|LBS|Entwicklung 0.8|Login view|[Hotel Dashboard](https://symfony.com/doc/current/security.html#authentication-identifying-logging-in-the-user)
16.05.2024|1.1|10:15-12:50|2h35min|LBS|Entwicklung 0.9|show Profile user|[Hotel Dashboard](https://symfony.com/doc/current/security.html#authentication-identifying-logging-in-the-user)
17.05.2024|1.1|10:15-12:50|2h35min|LBS|Entwicklung 1.10|Create Rooms Controller File Upload|[Hotel Dashboard](https://symfony.com/doc/current/controller/upload_file.html)
23.05.2024|1.1|10:15-12:50|2h35min|LBS|Projekt Lokal einrichten|Live Server einrichten|[Hetzner](https://www.hetzner.com)

## [Hiess]
---
Datum|AP NR|Zeitraum|Aufwand|Ort|Tätigkeit|Probleme|Quellen
-----|-----|--------|-------|---|---------|--------|-------
29.04.2024|1.1|7:30-10:00|2h15min|LBS|Ideenfindung|Thema Finden|[Hotel Dashboard](https://dribbble.com/shots/16194078-Tamago-Hotel-Booking-Dashboard-Exploration)
30.04.2024|1.1|7:30-09:00|1h30min|LBS|Projekt Handbuch 0.1|........|[Hotel Dashboard](https://www.eduvidual.at/)
02.05.2024|1.1|9:25-12:50|3h25min|LBS|Projekt Handbuch 0.2|Gantt Chart Vorlage / Arbeitspakete definieren|[Gantt Chart](https://www.onlinegantt.com/#/gantt)
03.05.2024|1.1|10:15-12:50|2h35min|LBS|Projekt Handbuch 0.3|Projekthandbuch / Arbeitspakete|[Hotel Dashboard](https://www.eduvidual.at/my/)
06.05.2024|1.1|10:00-12:20|2h20min|LBS|Projekt Handbuch 0.4|Arbeitspakete / Gantt Chart |[Gantt Chart](https://www.onlinegantt.com/#/gantt)
07.05.2024|1.1|07:30-09:00|1h30min|LBS|Projekt Handbuch 0.5|Gantt Chart anpassen mit Herrn Sengwein|[Gantt Chart](https://www.onlinegantt.com/#/gantt)
10.05.2024|1.1|10:15-12:50|2h35min|LBS|Projekt Handbuch 0.6|Arbeitspaket Personen Einteilung|[Hotel Dashboard](https://www.eduvidual.at/)
13.05.2024|1.1|10:00-12:20|2h20min|LBS|Projekt Handbuch 0.7|Risikoanalyse|[Hotel Dashboard](https://www.eduvidual.at/)
14.05.2024|1.1|10:00-12:20|2h20min|LBS|Design Mockup|Design Mockup Hotel Dashboard|[Hotel Dashboard](https://www.eduvidual.at/)
16.05.2024|1.1|10:15-12:50|2h35min|LBS|Design Mockup|Design Mockup Hotel Dashboard|[Hotel Dashboard](https://www.eduvidual.at/)
17.05.2024|1.1|10:15-12:50|2h35min|LBS|Projekt Lokal einrichten|Git Clone Error php Version|[PHP](https://www.php.net/manual/de/intro-whatis.php)
23.05.2024|1.1|10:15-12:50|2h35min|LBS|Projekt Lokal einrichten|Live Server einrichten|[Hetzner](https://www.hetzner.com)
23.05.2024|1.1|19:00-19:30|0h30min|LBS|Projekt auf Live Server kopieren|[Hetzner](https://www.hetzner.com)

