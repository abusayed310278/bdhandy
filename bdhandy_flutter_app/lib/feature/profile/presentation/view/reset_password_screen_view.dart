import 'package:flutter/material.dart';
import 'package:get/get.dart' hide Response;
import 'package:google_fonts/google_fonts.dart';
import 'package:dio/dio.dart';
import '../../../../core/colors/app_color.dart';
import '../../../../core/common/widgets/custom_snackbar.dart';

import '../../../../core/network/api_service.dart';
import 'login_screen_view.dart';

class ResetPasswordScreenView extends StatefulWidget {
  final String email;
  final String otp;

  const ResetPasswordScreenView({
    super.key,
    required this.email,
    required this.otp,
  });

  @override
  State<ResetPasswordScreenView> createState() => _ResetPasswordScreenViewState();
}

class _ResetPasswordScreenViewState extends State<ResetPasswordScreenView> {
  final passwordCtrl = TextEditingController();
  final confirmPasswordCtrl = TextEditingController();
  final formKey = GlobalKey<FormState>();
  bool _isLoading = false;

  @override
  void dispose() {
    passwordCtrl.dispose();
    confirmPasswordCtrl.dispose();
    super.dispose();
  }

  void _resetPassword() async {
    if (formKey.currentState!.validate()) {
      setState(() => _isLoading = true);
      try {
        final apiService = Get.find<ApiService>();
        final response = await apiService.resetPassword(
          email: widget.email,
          otp: widget.otp,
          password: passwordCtrl.text,
        );
        if (response.statusCode == 200 && response.data != null) {
          final body = response.data;
          // Backend returns status 200 on success, we don't strictly need body['success'] == true
          CustomSnackbar.showSuccess(
            title: 'Password Changed',
            message: body['message'] ?? 'Your password has been successfully reset.',
          );
          Get.offAll(() => const LoginScreenView());
          return;
        }
        
        CustomSnackbar.showError(message: 'Password reset failed. Please try again.');
      } on DioException catch (e) {
        final body = e.response?.data;
        String errorMsg = 'Password reset failed. Please try again.';
        if (body != null) {
          if (body['errors'] != null) {
            final Map errors = body['errors'];
            errorMsg = errors.values.map((v) => (v as List).join(', ')).join('\n');
          } else if (body['message'] != null) {
            errorMsg = body['message'];
          }
        }
        CustomSnackbar.showError(message: errorMsg);
      } catch (e) {
        CustomSnackbar.showError(message: 'Connection error. Please try again.');
      } finally {
        if (mounted) {
          setState(() => _isLoading = false);
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColor.background,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: GestureDetector(
          onTap: () => Get.back(),
          child: const Icon(Icons.arrow_back_ios_new,
              size: 18, color: AppColor.textPrimary),
        ),
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Form(
            key: formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Create new password',
                  style: GoogleFonts.poppins(
                    fontSize: 26,
                    fontWeight: FontWeight.w700,
                    color: AppColor.textPrimary,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  'Your new password must be unique from those previously used.',
                  style: GoogleFonts.poppins(
                    fontSize: 14,
                    color: AppColor.textSecondary,
                    height: 1.5,
                  ),
                ),
                const SizedBox(height: 36),

                // New Password
                _ResetPasswordField(
                  controller: passwordCtrl,
                  label: 'New Password',
                  hint: '••••••••',
                  icon: Icons.lock_outline,
                  validator: (v) => (v == null || v.isEmpty)
                      ? 'Enter your new password'
                      : null,
                ),

                const SizedBox(height: 16),

                // Confirm Password
                _ResetPasswordField(
                  controller: confirmPasswordCtrl,
                  label: 'Confirm Password',
                  hint: '••••••••',
                  icon: Icons.lock_reset_outlined,
                  validator: (v) {
                    if (v == null || v.isEmpty) {
                      return 'Confirm your new password';
                    }
                    if (v != passwordCtrl.text) {
                      return 'Passwords do not match';
                    }
                    return null;
                  },
                ),

                const SizedBox(height: 32),

                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: _isLoading ? null : _resetPassword,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColor.primary,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                      elevation: 0,
                    ),
                    child: _isLoading
                        ? const SizedBox(
                            height: 20,
                            width: 20,
                            child: CircularProgressIndicator(
                              color: Colors.white,
                              strokeWidth: 2,
                            ),
                          )
                        : Text(
                            'Reset Password',
                            style: GoogleFonts.poppins(
                              color: Colors.white,
                              fontSize: 15,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _ResetPasswordField extends StatefulWidget {
  final TextEditingController controller;
  final String label;
  final String hint;
  final IconData icon;
  final String? Function(String?)? validator;

  const _ResetPasswordField({
    required this.controller,
    required this.label,
    required this.hint,
    required this.icon,
    this.validator,
  });

  @override
  State<_ResetPasswordField> createState() => _ResetPasswordFieldState();
}

class _ResetPasswordFieldState extends State<_ResetPasswordField> {
  bool _obscure = true;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          widget.label,
          style: GoogleFonts.poppins(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            color: AppColor.textPrimary,
          ),
        ),
        const SizedBox(height: 8),
        TextFormField(
          controller: widget.controller,
          obscureText: _obscure,
          validator: widget.validator,
          style: GoogleFonts.poppins(fontSize: 14, color: AppColor.textPrimary),
          decoration: InputDecoration(
            hintText: widget.hint,
            hintStyle: GoogleFonts.poppins(fontSize: 13, color: AppColor.textHint),
            prefixIcon: Icon(widget.icon, color: AppColor.textSecondary, size: 20),
            suffixIcon: GestureDetector(
              onTap: () => setState(() => _obscure = !_obscure),
              child: Icon(
                _obscure ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                color: AppColor.textSecondary,
                size: 20,
              ),
            ),
            filled: true,
            fillColor: Colors.white,
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: const BorderSide(color: AppColor.primary, width: 1.5),
            ),
            errorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: const BorderSide(color: AppColor.error, width: 1.5),
            ),
            focusedErrorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: const BorderSide(color: AppColor.error, width: 1.5),
            ),
          ),
        ),
      ],
    );
  }
}
