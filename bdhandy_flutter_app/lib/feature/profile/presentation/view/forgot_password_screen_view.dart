import 'package:flutter/material.dart';
import 'package:get/get.dart' hide Response;
import 'package:google_fonts/google_fonts.dart';
import 'package:dio/dio.dart';
import '../../../../core/colors/app_color.dart';
import 'otp_verification_screen_view.dart';
import '../../../../core/network/api_service.dart';
import '../../../../core/common/widgets/custom_snackbar.dart';

class ForgotPasswordScreenView extends StatefulWidget {
  const ForgotPasswordScreenView({super.key});

  @override
  State<ForgotPasswordScreenView> createState() => _ForgotPasswordScreenViewState();
}

class _ForgotPasswordScreenViewState extends State<ForgotPasswordScreenView> {
  final emailCtrl = TextEditingController();
  final formKey = GlobalKey<FormState>();
  bool _isLoading = false;

  @override
  void dispose() {
    emailCtrl.dispose();
    super.dispose();
  }

  void _sendOtp() async {
    if (formKey.currentState!.validate()) {
      setState(() => _isLoading = true);
      try {
        final apiService = Get.find<ApiService>();
        final response = await apiService.sendResetOtp(emailCtrl.text.trim());
        if (response.statusCode == 200 && response.data != null) {
          final body = response.data;
          if (body['success'] == true) {
            CustomSnackbar.showSuccess(
              title: 'OTP Sent',
              message: body['message'] ?? 'Check your email for the reset code.',
            );
            Get.to(() => OtpVerificationScreenView(email: emailCtrl.text.trim()));
            return;
          }
        }
        
        CustomSnackbar.showError(message: 'Failed to send OTP. Please check your email.');
      } on DioException catch (e) {
        final body = e.response?.data;
        String errorMsg = 'Failed to send OTP. Please check your email.';
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
                  'Forgot Password?',
                  style: GoogleFonts.poppins(
                    fontSize: 26,
                    fontWeight: FontWeight.w700,
                    color: AppColor.textPrimary,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  'Don\'t worry! It occurs. Please enter the email address linked with your account.',
                  style: GoogleFonts.poppins(
                    fontSize: 14,
                    color: AppColor.textSecondary,
                    height: 1.5,
                  ),
                ),
                const SizedBox(height: 36),
                Text(
                  'Email Address',
                  style: GoogleFonts.poppins(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: AppColor.textPrimary,
                  ),
                ),
                const SizedBox(height: 8),
                TextFormField(
                  controller: emailCtrl,
                  keyboardType: TextInputType.emailAddress,
                  validator: (v) => (v == null || !v.contains('@'))
                      ? 'Enter a valid email'
                      : null,
                  style: GoogleFonts.poppins(
                      fontSize: 14, color: AppColor.textPrimary),
                  decoration: InputDecoration(
                    hintText: 'you@example.com',
                    hintStyle: GoogleFonts.poppins(
                        fontSize: 13, color: AppColor.textHint),
                    prefixIcon: const Icon(Icons.email_outlined,
                        color: AppColor.textSecondary, size: 20),
                    filled: true,
                    fillColor: Colors.white,
                    contentPadding: const EdgeInsets.symmetric(
                        horizontal: 16, vertical: 16),
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
                      borderSide:
                          const BorderSide(color: AppColor.primary, width: 1.5),
                    ),
                  ),
                ),
                const SizedBox(height: 32),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: _isLoading ? null : _sendOtp,
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
                            'Send OTP',
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
