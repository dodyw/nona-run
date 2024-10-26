# Nona.run - Free Webhook Testing Tool

Nona.run is a simple, fast, and reliable webhook testing tool designed for developers to test, debug, and monitor HTTP webhooks and API callbacks. This open-source project allows you to create custom endpoints, receive incoming webhook requests, and inspect the details of each request in real-time.

## Features

- Create multiple webhook endpoints
- Real-time webhook testing
- Detailed request inspection (headers, query parameters, body)
- Support for various HTTP methods (GET, POST, PUT, DELETE, etc.)
- Message history with automatic cleanup
- User-friendly interface
- No registration required


## Demo

You can try out Nona.run without installation by visiting our live demo site:

[https://nona.run](https://nona.run)

Experience the features and functionality firsthand before setting up your own instance.


## Requirements

- PHP 7.4 or higher
- SQLite3 extension for PHP
- Web server (e.g., Apache, Nginx)

## Installation

1. Clone this repository to your web server's document root:
   ```
   git clone https://github.com/dodyw/nona-run.git
   ```

2. Ensure the `webhook.db` file and its parent directory are writable by your web server:
   ```
   chmod 755 /path/to/nona-run
   chmod 664 /path/to/nona-run/webhook.db
   ```

3. Configure your web server to serve the project directory.

4. Access the tool through your web browser (e.g., `http://yourdomain.com/nona-run/`).

## Usage

1. Open the Nona.run interface in your web browser.
2. Click "Create New Endpoint" to generate a unique webhook URL.
3. Use the generated URL in your application or service that sends webhooks.
4. Watch incoming webhook requests appear in real-time on the Nona.run interface.
5. Click on individual requests to inspect their details (headers, query parameters, body).

## Configuration

You can modify the following constants in `config.php`:

- `MAX_MESSAGES`: Maximum number of messages stored per endpoint
- `COOKIE_LIFETIME`: Duration of the user session cookie
- `DB_FILE`: Path to the SQLite database file

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open source and available under the [MIT License](LICENSE).


## Contact

For inquiries, contributions, or project collaborations, you can reach out to the developers:

- Dody Rachmat Wicaksono
  - Email: dodyrw@gmail.com
  - Website: [nicecoder.com](https://www.nicecoder.com)
  - Open to various project opportunities

We welcome any potential collaborations or project ideas related to Nona.run or other development initiatives.

