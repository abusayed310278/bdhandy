import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'app_ground_view.dart';
import 'feature/home/presentation/controller/home_controller.dart';
import 'core/network/api_service.dart';
import 'core/network/session_manager.dart';
import 'feature/onboarding/presentation/view/splash_screen.dart';

import 'core/storage/favorites_manager.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  HttpOverrides.global = MyHttpOverrides();

  // Initialize local preferences first to prevent LateInitializationErrors
  await SessionManager.init();
  await Get.putAsync(() => FavoritesManager().init());

  // ApiService MUST be registered before HomeController because
  // HomeController.onInit() immediately calls Get.find<ApiService>()
  final apiService = Get.put(ApiService());
  final homeController = Get.put(HomeController());

  // Restore session
  final token = SessionManager.getToken();
  if (token != null) {
    apiService.setToken(token);
    final uData = SessionManager.getUserData();
    homeController.updateUserData(uData);
  }

  runApp(const BdhandyApp());
}

class BdhandyApp extends StatelessWidget {
  const BdhandyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return GetMaterialApp(
      title: 'BDHANDY',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF6C3EE8),
          brightness: Brightness.light,
        ),
        useMaterial3: true,
        fontFamily: 'Poppins',
      ),
      home: SessionManager.isLoggedIn() ? const AppGroundView() : const SplashScreen(),
    );
  }
}

class MyHttpOverrides extends HttpOverrides {
  @override
  HttpClient createHttpClient(SecurityContext? context) {
    return super.createHttpClient(context)
      ..badCertificateCallback =
          (X509Certificate cert, String host, int port) => true;
  }
}
