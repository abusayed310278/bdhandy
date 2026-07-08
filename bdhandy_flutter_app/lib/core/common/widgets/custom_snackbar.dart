import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';

class CustomSnackbar {
  static void showSuccess({required String message, String title = "Success"}) {
    _showSnackbar(
      title: title,
      message: message,
      backgroundColor: const Color(0xFFE5F4EB),
      textColor: const Color(0xFF2EBD85),
      icon: Icons.check_circle_outline,
    );
  }

  static void showError({required String message, String title = "Error"}) {
    _showSnackbar(
      title: title,
      message: message,
      backgroundColor: const Color(0xFFFEECEB),
      textColor: const Color(0xFFF04438),
      icon: Icons.error_outline,
    );
  }

  static void showWarning({required String message, String title = "Warning"}) {
    _showSnackbar(
      title: title,
      message: message,
      backgroundColor: const Color(0xFFFFFAEB),
      textColor: const Color(0xFFF79009),
      icon: Icons.warning_amber_rounded,
    );
  }

  static void _showSnackbar({
    required String title,
    required String message,
    required Color backgroundColor,
    required Color textColor,
    required IconData icon,
  }) {
    if (Get.isSnackbarOpen) Get.closeCurrentSnackbar();

    Get.snackbar(
      '',
      '',
      snackPosition: SnackPosition.TOP,
      backgroundColor: Colors.white,
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      borderRadius: 24,
      borderWidth: 1.5,
      borderColor: textColor.withOpacity(0.3),
      boxShadows: [
        BoxShadow(
          color: textColor.withOpacity(0.08),
          blurRadius: 20,
          offset: const Offset(0, 8),
        ),
        BoxShadow(
          color: Colors.black.withOpacity(0.04),
          blurRadius: 10,
          offset: const Offset(0, 4),
        ),
      ],
      titleText: const SizedBox.shrink(),
      messageText: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: backgroundColor,
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: textColor, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  title,
                  style: GoogleFonts.poppins(
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    color: const Color(0xFF1E293B),
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  message,
                  style: GoogleFonts.poppins(
                    fontSize: 11,
                    fontWeight: FontWeight.w500,
                    color: const Color(0xFF64748B),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
