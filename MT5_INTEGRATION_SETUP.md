# MT5 Integration Setup Guide

This guide will help you set up the MetaTrader 5 integration to connect your Laravel application with real MT5 accounts.

## Prerequisites

- Python 3.7 or higher
- MetaTrader 5 platform installed (Windows only for full functionality)
- MT5 account credentials (login, password, server)

## Installation Steps

### 1. Install Python and MetaTrader5 Package

The application uses a Python bridge to communicate with MetaTrader 5. You need to install the MetaTrader5 Python package:

```bash
# Install MetaTrader5 package
pip install MetaTrader5

# Or using pip3
pip3 install MetaTrader5
```

### 2. Verify Python Installation

Make sure Python 3 is accessible from your command line:

```bash
python3 --version
# Should output: Python 3.x.x
```

### 3. Make the Bridge Script Executable

```bash
chmod +x scripts/mt5_bridge.py
```

### 4. Test the Bridge Script

You can test the bridge script manually:

```bash
python3 scripts/mt5_bridge.py test_connection '{"login":"YOUR_ACCOUNT","password":"YOUR_PASSWORD","server":"YOUR_SERVER"}'
```

## Platform-Specific Notes

### Windows

The MetaTrader5 Python package works best on Windows where MetaTrader 5 is installed. The package will automatically detect and connect to the installed MT5 terminal.

**Requirements:**
- MetaTrader 5 platform must be installed
- Python 3.7+ (64-bit recommended)

### Linux

On Linux, you have two options:

1. **Use Wine to run MT5**: Install MetaTrader 5 via Wine and connect through it
2. **Use a Remote MT5 Server**: Connect to a Windows machine running MT5 remotely

### macOS

Similar to Linux, you'll need to either:
- Use Wine/CrossOver to run MT5
- Connect to a remote Windows machine with MT5

## Configuration

### Broker List

The application includes a pre-configured list of popular MT5 brokers in `config/mt5_brokers.php`. You can add or modify brokers and their servers:

```php
'Your Broker Name' => [
    'servers' => [
        'ServerName1' => 'ServerName1',
        'ServerName2' => 'ServerName2',
    ],
],
```

### Testing Connection

Before adding an account, you can test the connection using the "Test Connection" button in the account form. This will verify:
- Python bridge is working
- MT5 package is installed
- Credentials are correct
- Connection to broker server is successful

## Troubleshooting

### Error: "MT5 Python bridge not found"

The application will fall back to simulation mode if the Python bridge is not available. This is for demonstration purposes only. To fix:
1. Ensure `scripts/mt5_bridge.py` exists
2. Verify Python 3 is installed
3. Check file permissions

### Error: "MT5 initialization failed"

This usually means:
- MetaTrader 5 is not installed (Windows)
- Python cannot access the MT5 terminal
- MT5 platform is already running with a different account

**Solution:** Close all MT5 instances and try again.

### Error: "Login failed"

Check:
- Account number (login) is correct
- Password is correct (use trading password, not investor password)
- Server name matches exactly (case-sensitive)
- Account is not locked or expired

### Error: "Failed to get account info"

This can occur if:
- Connection was established but account has no permissions
- Account is read-only (investor password used instead of trading password)
- Server is experiencing issues

## Security Notes

- **Passwords are encrypted**: All account passwords are encrypted in the database using Laravel's encryption
- **Never share credentials**: Keep your MT5 credentials secure
- **Use Investor Password**: For read-only monitoring, consider using the investor password instead of the trading password
- **Server Security**: Ensure your server has proper firewall rules if exposing the application publicly

## API Endpoints Used

The bridge supports the following actions:

1. **test_connection**: Tests connection and retrieves basic account info
2. **get_account_info**: Gets detailed account information including open positions
3. **get_trades**: Retrieves all open trades/positions

## Feature Availability

| Feature | Status | Notes |
|---------|--------|-------|
| Account Connection | ✅ Implemented | Connects to real MT5 accounts |
| Real-time Balance | ✅ Implemented | Syncs balance, equity, margin, etc. |
| Open Positions | ✅ Implemented | Retrieves all open trades |
| Trade History | 🚧 Planned | Coming soon |
| Place Orders | 🚧 Planned | For trade copying feature |
| Modify Orders | 🚧 Planned | For trade management |

## Alternative: MetaTrader Web API

If you prefer not to use Python, you can integrate with MetaTrader Web API directly:

1. Contact your broker for Web API access
2. Update `Mt5Service.php` to use HTTP requests instead of Python bridge
3. Implement OAuth/API token authentication as required by your broker

## Support

For issues related to:
- **MetaTrader5 Python package**: Visit https://www.mql5.com/en/docs/python_metatrader5
- **Application integration**: Create an issue in the repository
- **Broker-specific problems**: Contact your broker's support

## Development Mode

During development or if Python bridge is not available, the application runs in "simulation mode" which returns dummy data. This is indicated in the connection test message.

To disable simulation mode and require real MT5 connections, modify the `simulateMt5Response` method in `Mt5Service.php` to return an error instead.
