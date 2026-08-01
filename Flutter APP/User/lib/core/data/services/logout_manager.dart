import 'package:ovopay/core/utils/util_exporter.dart';

class LogoutManager {
  // Singleton instance
  static final LogoutManager _instance = LogoutManager._internal();

  // Private constructor for singleton
  LogoutManager._internal();

  // Factory constructor to return the singleton instance
  factory LogoutManager() {
    return _instance;
  }

  // Flag to check if a logout is in progress
  bool _isLoggingOut = false;

  // Method to start the logout process
  Future<void> startLogout(Function logoutAction) async {
    if (_isLoggingOut) {
      printX('Logout is already in progress.');
      return; // Prevent further logouts
    }

    _isLoggingOut = true;

    try {
      // Call the actual logout function passed to this method
      await logoutAction();
    } catch (e) {
      printE('Error during logout: $e');
    } finally {
      // Reset the flag after the logout process completes
      // _isLoggingOut = false;
    }
  }

  // Getter to check if a logout is already happening
  bool get isLoggingOut => _isLoggingOut;
  // Setter to reset the _isLoggingOut flag
  set resetLoggingOut(bool value) {
    _isLoggingOut = value;
  }
}
