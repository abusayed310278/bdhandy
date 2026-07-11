import 'package:flutter/material.dart';
import 'package:get/get.dart' hide Response;
import 'package:google_fonts/google_fonts.dart';
import 'package:dio/dio.dart';
import '../../../../core/colors/app_color.dart';
import 'forgot_password_screen_view.dart';
import 'signup_screen_view.dart';
import '../../../../core/common/widgets/custom_snackbar.dart';

import '../../../../core/network/api_service.dart';
import 'profile_screen_view.dart';
import '../../../../app_ground_view.dart';
import '../../../home/presentation/controller/home_controller.dart';
import '../../../../core/network/session_manager.dart';

class LoginScreenView extends StatefulWidget {
  const LoginScreenView({super.key});

  @override
  State<LoginScreenView> createState() => _LoginScreenViewState();
}

class _LoginScreenViewState extends State<LoginScreenView> {
  final emailCtrl = TextEditingController();
  final passwordCtrl = TextEditingController();
  final formKey = GlobalKey<FormState>();
  bool _isLoading = false;

  @override
  void dispose() {
    emailCtrl.dispose();
    passwordCtrl.dispose();
    super.dispose();
  }

  void _login() async {
    if (formKey.currentState!.validate()) {
      setState(() => _isLoading = true);
      // Simulate API delay
      await Future.delayed(const Duration(seconds: 1));

      if (emailCtrl.text.trim() == 'a@gmail.com' && passwordCtrl.text == '11111111') {
        // Save dummy session
        final dummyUserData = {
          'id': 1,
          'name': 'Demo User',
          'email': 'a@gmail.com',
          'contact': '1234567890',
        };
        await SessionManager.saveSession('dummy_token_123', dummyUserData);
        Get.find<HomeController>().updateUserData(dummyUserData);

        CustomSnackbar.showSuccess(
          title: 'Login Successful',
          message: 'Welcome back!',
        );
        Get.find<HomeController>().currentIndex.value = 0;
        Get.offAll(() => const AppGroundView());
      } else {
        CustomSnackbar.showError(message: 'Invalid email or password.');
      }
      
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColor.background,
      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            children: [
              // ── Purple Header ───────────────────────────────────────
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(24, 48, 24, 40),
                decoration: const BoxDecoration(
                  color: Colors.transparent,
                ),
                child: Column(
                  children: [
                    Align(
                      alignment: Alignment.centerLeft,
                      child: GestureDetector(
                        onTap: () => Get.back(),
                        child: Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: AppColor.primary.withOpacity(0.1),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.arrow_back_ios_new,
                              color: AppColor.primary, size: 18),
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    // Lock icon in circle
                    Container(
                      width: 84,
                      height: 84,
                      decoration: BoxDecoration(
                        color: AppColor.primaryLight,
                        shape: BoxShape.circle,
                        border: Border.all(
                            color: AppColor.primary.withOpacity(0.2), width: 2),
                      ),
                      child: const Icon(Icons.lock_outline,
                          color: AppColor.primary, size: 42),
                    ),
                    const SizedBox(height: 18),
                    Text(
                      'Welcome Back',
                      style: GoogleFonts.poppins(
                        color: AppColor.textPrimary,
                        fontSize: 22,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'Sign in to manage your booking, master & schedule',
                      textAlign: TextAlign.center,
                      style: GoogleFonts.poppins(
                        color: AppColor.textSecondary,
                        fontSize: 13,
                        height: 1.5,
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 16),

              // ── Login Form ──────────────────────────────────────────
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: Form(
                  key: formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Email
                      _LoginField(
                        controller: emailCtrl,
                        label: 'Email Address',
                        hint: 'you@example.com',
                        icon: Icons.email_outlined,
                        keyboardType: TextInputType.emailAddress,
                        validator: (v) => (v == null || !v.contains('@'))
                            ? 'Enter a valid email'
                            : null,
                      ),

                      const SizedBox(height: 20),

                      // Password
                      _LoginField(
                        controller: passwordCtrl,
                        label: 'Password',
                        hint: '••••••••',
                        icon: Icons.lock_outline,
                        obscureText: true,
                        validator: (v) => (v == null || v.length < 6)
                            ? 'Password must be at least 6 characters'
                            : null,
                      ),

                      const SizedBox(height: 10),

                      // Forgot password
                      Align(
                        alignment: Alignment.centerRight,
                        child: GestureDetector(
                          onTap: () => Get.to(() => const ForgotPasswordScreenView()),
                          child: Text(
                            'Forgot Password?',
                            style: GoogleFonts.poppins(
                              fontSize: 13,
                              color: AppColor.primary,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ),

                      const SizedBox(height: 28),

                      // Login button
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: _isLoading ? null : _login,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColor.primary,
                            padding:
                                const EdgeInsets.symmetric(vertical: 16),
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
                                  'Login',
                                  style: GoogleFonts.poppins(
                                    color: Colors.white,
                                    fontSize: 15,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                        ),
                      ),

                      const SizedBox(height: 32),

                      // Sign up
                      Center(
                        child: RichText(
                          text: TextSpan(
                            text: "Don't have an account? ",
                            style: GoogleFonts.poppins(
                              fontSize: 13,
                              color: AppColor.textSecondary,
                            ),
                            children: [
                              WidgetSpan(
                                child: GestureDetector(
                                  onTap: () async {
                                    final result = await Get.to(() => const SignupScreenView());
                                    if (result == true) {
                                      CustomSnackbar.showSuccess(
                                        title: 'Success',
                                        message: 'Account created successfully! Please login.',
                                      );
                                    }
                                  },
                                  child: Text(
                                    'Sign Up',
                                    style: GoogleFonts.poppins(
                                      fontSize: 13,
                                      color: AppColor.primary,
                                      fontWeight: FontWeight.w700,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),

                      const SizedBox(height: 32),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────
//  Login Text Field widget
// ─────────────────────────────────────────────────────────────────────────
class _LoginField extends StatefulWidget {
  final TextEditingController controller;
  final String label;
  final String hint;
  final IconData icon;
  final bool obscureText;
  final TextInputType keyboardType;
  final String? Function(String?)? validator;

  const _LoginField({
    required this.controller,
    required this.label,
    required this.hint,
    required this.icon,
    this.obscureText = false,
    this.keyboardType = TextInputType.text,
    this.validator,
  });

  @override
  State<_LoginField> createState() => _LoginFieldState();
}

class _LoginFieldState extends State<_LoginField> {
  late bool _obscure;

  @override
  void initState() {
    super.initState();
    _obscure = widget.obscureText;
  }

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
          keyboardType: widget.keyboardType,
          validator: widget.validator,
          style: GoogleFonts.poppins(
              fontSize: 14, color: AppColor.textPrimary),
          decoration: InputDecoration(
            hintText: widget.hint,
            hintStyle: GoogleFonts.poppins(
                fontSize: 13, color: AppColor.textHint),
            prefixIcon:
                Icon(widget.icon, color: AppColor.textSecondary, size: 20),
            suffixIcon: widget.obscureText
                ? GestureDetector(
                    onTap: () => setState(() => _obscure = !_obscure),
                    child: Icon(
                      _obscure
                          ? Icons.visibility_off_outlined
                          : Icons.visibility_outlined,
                      color: AppColor.textSecondary,
                      size: 20,
                    ),
                  )
                : null,
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
            errorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide:
                  const BorderSide(color: AppColor.error, width: 1.5),
            ),
            focusedErrorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide:
                  const BorderSide(color: AppColor.error, width: 1.5),
            ),
          ),
        ),
      ],
    );
  }
}
