# MT5 Account Analyzer

A comprehensive Laravel 12 web application with Livewire for MetaTrader 5 account analysis and management. This application helps traders monitor their trading accounts, visualize performance metrics, and manage trade copying between multiple accounts.

## Features

### 🔐 Authentication & User Management
- Secure user registration and login with Laravel Breeze
- Profile management
- Multi-user support

### 📊 Account Management
- Add and manage multiple MT5 accounts
- Store account credentials securely
- Sync account data from MT5 servers
- Track account balance, equity, margin, and profit
- Monitor account status and last sync time

### 📈 Performance Analytics
- Interactive dashboard with real-time account metrics
- Visual charts for:
  - Balance and Equity trends
  - Profit/Loss tracking
  - Drawdown analysis
- Historical performance data
- Daily statistics tracking

### 💹 Trade Management
- View recent trades
- Track open and closed positions
- Monitor trade profitability
- Commission and swap tracking
- Trade history with detailed information

### 🔄 Trade Copy System
- Copy trades between your MT5 accounts
- Configure copy rules with:
  - Source and destination accounts
  - Lot multiplier (scale position sizes)
  - Optional Stop Loss copying
  - Optional Take Profit copying
  - Symbol filtering (copy specific pairs only)
- Enable/disable copy rules on demand
- Real-time trade synchronization

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Livewire 3 with Alpine.js
- **Styling**: Tailwind CSS
- **Charts**: Chart.js
- **Database**: SQLite (default) / MySQL / PostgreSQL
- **Authentication**: Laravel Breeze

## Installation

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js and NPM
- SQLite/MySQL/PostgreSQL

### Setup Instructions

1. Clone the repository:
```bash
git clone https://github.com/alslimany/mt5-app.git
cd mt5-app
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env` file:
```env
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite

# Or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=mt5_app
# DB_USERNAME=root
# DB_PASSWORD=
```

7. Run database migrations:
```bash
php artisan migrate
```

8. Build frontend assets:
```bash
npm run build
# or for development with hot reload:
npm run dev
```

9. Start the development server:
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Usage

### Adding an MT5 Account

1. Register/Login to the application
2. Navigate to the "Accounts" page
3. Click "Add Account"
4. Fill in the account details:
   - Account Name (friendly name)
   - Account Number
   - Broker name
   - Server address
   - API credentials (optional)
   - Initial balance
   - Currency
   - Leverage

### Setting Up Trade Copy

1. Navigate to the "Trade Copy" page
2. Click "Add Copy Rule"
3. Configure the rule:
   - Select source account (trades to copy from)
   - Select destination account (where to copy trades)
   - Set lot multiplier (e.g., 0.5 for half size, 2.0 for double)
   - Choose to copy Stop Loss and Take Profit
   - Add symbol filter if needed (e.g., "EURUSD,GBPUSD")
4. The rule will automatically copy new trades

## MT5 Integration

**Note**: The current implementation includes a placeholder MT5 service. For production use, you'll need to integrate with actual MT5 APIs:

- **MetaTrader 5 Web API**: For REST-based integration
- **MetaTrader Manager API**: For direct broker integration
- **Custom Bridge**: Using socket connections or webhooks

The `Mt5Service` class in `app/Services/Mt5Service.php` is designed to be extended with real MT5 API implementation.

## Database Schema

### Tables

- **users**: User accounts
- **mt5_accounts**: MT5 trading accounts
- **mt5_trades**: Individual trades
- **mt5_account_stats**: Daily account statistics
- **trade_copy_rules**: Trade copying configuration

## Security

- All sensitive data (API tokens, secrets) are encrypted
- User authentication required for all features
- Account access restricted to owner only
- CSRF protection on all forms
- SQL injection protection via Eloquent ORM

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Building for Production
```bash
npm run build
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
