import 'dart:convert';
import 'package:get/get.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../feature/home/data/model/salon_model.dart';

class FavoritesManager extends GetxService {
  late SharedPreferences _prefs;
  final String _key = 'saved_salons';

  final savedSalons = <Salon>[].obs;

  Future<FavoritesManager> init() async {
    _prefs = await SharedPreferences.getInstance();
    _loadFavorites();
    return this;
  }

  void _loadFavorites() {
    final String? data = _prefs.getString(_key);
    if (data != null && data.isNotEmpty) {
      try {
        final List<dynamic> decoded = jsonDecode(data);
        savedSalons.value = decoded.map((e) => Salon.fromJson(e)).toList();
      } catch (e) {
        print('Error loading favorites: $e');
      }
    }
  }

  void _saveFavorites() {
    final String data = jsonEncode(savedSalons.map((e) => e.toJson()).toList());
    _prefs.setString(_key, data);
  }

  bool isFavorite(String slug) {
    return savedSalons.any((s) => s.slug == slug);
  }

  void toggleFavorite(Salon salon) {
    if (isFavorite(salon.slug)) {
      savedSalons.removeWhere((s) => s.slug == salon.slug);
    } else {
      savedSalons.add(salon);
    }
    _saveFavorites();
  }
}
