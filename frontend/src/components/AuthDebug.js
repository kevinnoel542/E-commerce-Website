import React from 'react';
import { useApp } from '../context/AppContext';

const AuthDebug = () => {
  const { user, isAuthenticated, loading, error } = useApp();

  if (process.env.NODE_ENV === 'production') {
    return null; // Don't show in production
  }

  return (
    <div className="fixed bottom-4 left-4 bg-gray-800 text-white p-4 rounded-lg shadow-lg max-w-sm z-50">
      <h3 className="font-bold text-sm mb-2">🔍 Auth Debug</h3>
      <div className="text-xs space-y-1">
        <div>
          <strong>Loading:</strong> {loading ? '✅ Yes' : '❌ No'}
        </div>
        <div>
          <strong>Authenticated:</strong> {isAuthenticated ? '✅ Yes' : '❌ No'}
        </div>
        <div>
          <strong>User:</strong> {user ? `${user.email || 'No email'}` : '❌ None'}
        </div>
        <div>
          <strong>Error:</strong> {error || '✅ None'}
        </div>
        <div>
          <strong>LocalStorage:</strong> {localStorage.getItem('user') ? '✅ Has data' : '❌ Empty'}
        </div>
        {user && user.tokens && (
          <div>
            <strong>Token:</strong> {user.tokens.access_token ? '✅ Present' : '❌ Missing'}
          </div>
        )}
      </div>
    </div>
  );
};

export default AuthDebug;
