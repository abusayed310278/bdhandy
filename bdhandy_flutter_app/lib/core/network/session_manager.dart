import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class SessionManager {
  static const String _keyToken = 'user_token';
  static const String _keyUserData = 'user_data';

  static late final SharedPreferences _prefs;

  // Initialize SharedPreferences
  static Future<void> init() async {
    _prefs = await SharedPreferences.getInstance();
  }

  // Save Session
  static Future<void> saveSession(String token, Map<String, dynamic> userData) async {
    await _prefs.setString(_keyToken, token);
    await _prefs.setString(_keyUserData, jsonEncode(userData));
  }

  // Clear Session
  static Future<void> clearSession() async {
    await _prefs.remove(_keyToken);
    await _prefs.remove(_keyUserData);
  }

  // Get Token
  static String? getToken() {
    return _prefs.getString(_keyToken);
  }

  // Get User Data
  static Map<String, dynamic>? getUserData() {
    final str = _prefs.getString(_keyUserData);
    if (str != null) {
      try {
        return jsonDecode(str) as Map<String, dynamic>;
      } catch (_) {}
    }
    return null;
  }

  // Is Logged In
  static bool isLoggedIn() {
    return getToken() != null;
  }
}
