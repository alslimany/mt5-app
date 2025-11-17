#!/usr/bin/env python3
"""
MetaTrader 5 Python Bridge

This script provides a bridge between Laravel and MetaTrader 5 using the MetaTrader5 Python package.

Requirements:
    pip install MetaTrader5

Usage:
    python3 mt5_bridge.py <action> <params_json>

Actions:
    - test_connection: Test connection to MT5 account
    - get_account_info: Get account information
    - get_trades: Get open trades
    - get_history: Get trade history

Author: MT5 App
Date: 2025-11-17
"""

import sys
import json
import MetaTrader5 as mt5
from datetime import datetime, timedelta


def test_connection(params):
    """Test connection to MT5 account"""
    try:
        login = int(params.get('login'))
        password = params.get('password')
        server = params.get('server')
        
        # Initialize MT5
        if not mt5.initialize():
            return {
                'success': False,
                'message': f"MT5 initialization failed: {mt5.last_error()}"
            }
        
        # Login to the account
        authorized = mt5.login(login=login, password=password, server=server)
        
        if not authorized:
            error = mt5.last_error()
            mt5.shutdown()
            return {
                'success': False,
                'message': f"Login failed: {error}"
            }
        
        # Get account info
        account_info = mt5.account_info()
        
        if account_info is None:
            mt5.shutdown()
            return {
                'success': False,
                'message': "Failed to get account info"
            }
        
        # Convert account info to dict
        data = {
            'balance': float(account_info.balance),
            'equity': float(account_info.equity),
            'margin': float(account_info.margin),
            'free_margin': float(account_info.margin_free),
            'profit': float(account_info.profit),
            'credit': float(account_info.credit),
            'currency': account_info.currency,
            'leverage': int(account_info.leverage),
            'server': account_info.server,
            'name': account_info.name,
            'company': account_info.company,
        }
        
        # Shutdown connection
        mt5.shutdown()
        
        return {
            'success': True,
            'message': 'Connection successful',
            'data': data
        }
        
    except Exception as e:
        if mt5.initialize():
            mt5.shutdown()
        return {
            'success': False,
            'message': f"Error: {str(e)}"
        }


def get_account_info(params):
    """Get account information"""
    try:
        login = int(params.get('login'))
        password = params.get('password')
        server = params.get('server')
        
        # Initialize and login
        if not mt5.initialize():
            return {
                'success': False,
                'message': f"MT5 initialization failed: {mt5.last_error()}"
            }
        
        if not mt5.login(login=login, password=password, server=server):
            error = mt5.last_error()
            mt5.shutdown()
            return {
                'success': False,
                'message': f"Login failed: {error}"
            }
        
        # Get account info
        account_info = mt5.account_info()
        
        if account_info is None:
            mt5.shutdown()
            return {
                'success': False,
                'message': "Failed to get account info"
            }
        
        # Get open positions
        positions = mt5.positions_get()
        trades_data = []
        
        if positions is not None:
            for pos in positions:
                trades_data.append({
                    'ticket': str(pos.ticket),
                    'symbol': pos.symbol,
                    'type': 'buy' if pos.type == 0 else 'sell',
                    'volume': float(pos.volume),
                    'open_price': float(pos.price_open),
                    'current_price': float(pos.price_current),
                    'stop_loss': float(pos.sl) if pos.sl > 0 else None,
                    'take_profit': float(pos.tp) if pos.tp > 0 else None,
                    'profit': float(pos.profit),
                    'commission': float(pos.commission),
                    'swap': float(pos.swap),
                    'open_time': datetime.fromtimestamp(pos.time).isoformat(),
                    'comment': pos.comment if pos.comment else None,
                    'status': 'open',
                })
        
        # Convert account info to dict
        data = {
            'balance': float(account_info.balance),
            'equity': float(account_info.equity),
            'margin': float(account_info.margin),
            'free_margin': float(account_info.margin_free),
            'profit': float(account_info.profit),
            'credit': float(account_info.credit),
            'currency': account_info.currency,
            'leverage': int(account_info.leverage),
            'trades': trades_data,
        }
        
        # Shutdown connection
        mt5.shutdown()
        
        return {
            'success': True,
            'data': data
        }
        
    except Exception as e:
        if mt5.initialize():
            mt5.shutdown()
        return {
            'success': False,
            'message': f"Error: {str(e)}"
        }


def get_trades(params):
    """Get open trades"""
    try:
        login = int(params.get('login'))
        password = params.get('password')
        server = params.get('server')
        
        # Initialize and login
        if not mt5.initialize():
            return {
                'success': False,
                'message': f"MT5 initialization failed: {mt5.last_error()}"
            }
        
        if not mt5.login(login=login, password=password, server=server):
            error = mt5.last_error()
            mt5.shutdown()
            return {
                'success': False,
                'message': f"Login failed: {error}"
            }
        
        # Get open positions
        positions = mt5.positions_get()
        trades_data = []
        
        if positions is not None:
            for pos in positions:
                trades_data.append({
                    'ticket': str(pos.ticket),
                    'symbol': pos.symbol,
                    'type': 'buy' if pos.type == 0 else 'sell',
                    'volume': float(pos.volume),
                    'open_price': float(pos.price_open),
                    'current_price': float(pos.price_current),
                    'stop_loss': float(pos.sl) if pos.sl > 0 else None,
                    'take_profit': float(pos.tp) if pos.tp > 0 else None,
                    'profit': float(pos.profit),
                    'commission': float(pos.commission),
                    'swap': float(pos.swap),
                    'open_time': datetime.fromtimestamp(pos.time).isoformat(),
                    'comment': pos.comment if pos.comment else None,
                    'status': 'open',
                })
        
        # Shutdown connection
        mt5.shutdown()
        
        return {
            'success': True,
            'data': {'trades': trades_data}
        }
        
    except Exception as e:
        if mt5.initialize():
            mt5.shutdown()
        return {
            'success': False,
            'message': f"Error: {str(e)}"
        }


def main():
    if len(sys.argv) < 3:
        print(json.dumps({
            'success': False,
            'message': 'Usage: python3 mt5_bridge.py <action> <params_json>'
        }))
        sys.exit(1)
    
    action = sys.argv[1]
    params_json = sys.argv[2]
    
    try:
        params = json.loads(params_json)
    except json.JSONDecodeError as e:
        print(json.dumps({
            'success': False,
            'message': f'Invalid JSON parameters: {str(e)}'
        }))
        sys.exit(1)
    
    # Route to appropriate function
    actions = {
        'test_connection': test_connection,
        'get_account_info': get_account_info,
        'get_trades': get_trades,
    }
    
    if action not in actions:
        print(json.dumps({
            'success': False,
            'message': f'Unknown action: {action}'
        }))
        sys.exit(1)
    
    # Execute action
    result = actions[action](params)
    
    # Output result as JSON
    print(json.dumps(result))


if __name__ == '__main__':
    main()
