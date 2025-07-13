import React from 'react';

const Settings = () => {
  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-gray-900">Settings</h1>
      
      <div className="bg-white shadow rounded-lg">
        <div className="px-6 py-4 border-b border-gray-200">
          <h3 className="text-lg font-medium text-gray-900">System Settings</h3>
        </div>
        <div className="p-6">
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700">Site Name</label>
              <input type="text" defaultValue="MyStore" className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700">Site Description</label>
              <textarea rows={3} defaultValue="Your premium e-commerce store" className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700">Contact Email</label>
              <input type="email" defaultValue="admin@mystore.com" className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2" />
            </div>
            <div className="pt-4">
              <button className="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Save Settings
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Settings;
