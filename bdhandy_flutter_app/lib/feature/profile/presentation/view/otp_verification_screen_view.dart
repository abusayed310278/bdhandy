import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:dio/dio.dart';
import '../../../../core/colors/app_color.dart';
import '../../../../core/common/widgets/custom_snackbar.dart';
import '../../../../core/network/api_service.dart';
import 'reset_password_screen_view.dart';

import 'login_screen_view.dart';

class OtpVerificationScreenView extends StatefulWidget {
  final String email;
  final bool isRegistration;

  const OtpVerificationScreenView({
    super.key,
    required this.email,
    this.isRegistration = false,
  });

  @override
  State<OtpVerificationScreenView> createState() => _OtpVerificationScreenViewState();
}

class _OtpVerificationScreenViewState extends State<OtpVerificationScreenView> {
  final _ctrl1 = TextEditingController();
  final _ctrl2 = TextEditingController();
  final _ctrl3 = TextEditingController();
  final _ctrl4 = TextEditingController();
  bool _isResending = false;

  @override
  void dispose() {
    _ctrl1.dispose();
    _ctrl2.dispose();
    _ctrl3.dispose();
    _ctrl4.dispose();
    super.dispose();
  }

  void _verifyOtp() {
    final code = _ctrl1.text + _ctrl2.text + _ctrl3.text + _ctrl4.text;
    if (code.length < 4) {
      CustomSnackbar.showError(message: 'Please enter all 4 digits of the OTP.');
      return;
    }
    
    if (widget.isRegistration) {
      CustomSnackbar.showSuccess(title: 'Verified', message: 'Account verified successfully!');
      Get.offAll(() => const LoginScreenView());
    } else {
      Get.to(() => ResetPasswordScreenView(email: widget.email, otp: code));
    }
  }

  void _resendOtp() async {
    if (_isResending) return;
    setState(() => _isResending = true);
    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.sendResetOtp(widget.email);
      if (response.statusCode == 200 && response.data != null) {
        final body = response.data;
        if (body['success'] == true) {
          CustomSnackbar.showSuccess(
            title: 'OTP Resent',
            message: body['message'] ?? 'A new verification code has been sent.',
          );
          return;
        }
      }
      CustomSnackbar.showError(message: 'Failed to resend OTP. Please try again.');
    } on DioException catch (e) {
      final body = e.response?.data;
      String errorMsg = 'Failed to resend OTP. Please try again.';
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
        setState(() => _isResending = false);
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
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'OTP Verification',
                style: GoogleFonts.poppins(
                  fontSize: 26,
                  fontWeight: FontWeight.w700,
                  color: AppColor.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              Text(
                'Enter the verification code we just sent to:\n${widget.email}',
                style: GoogleFonts.poppins(
                  fontSize: 14,
                  color: AppColor.textSecondary,
                  height: 1.5,
                ),
              ),
              const SizedBox(height: 36),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  _otpBox(context, _ctrl1),
                  _otpBox(context, _ctrl2),
                  _otpBox(context, _ctrl3),
                  _otpBox(context, _ctrl4),
                ],
              ),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _verifyOtp,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColor.primary,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                    elevation: 0,
                  ),
                  child: Text(
                    'Verify',
                    style: GoogleFonts.poppins(
                      color: Colors.white,
                      fontSize: 15,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 24),
              Center(
                child: RichText(
                  text: TextSpan(
                    text: "Didn't receive code? ",
                    style: GoogleFonts.poppins(
                      fontSize: 13,
                      color: AppColor.textSecondary,
                    ),
                    children: [
                      WidgetSpan(
                        child: GestureDetector(
                          onTap: _isResending ? null : _resendOtp,
                          child: Text(
                            _isResending ? 'Resending...' : 'Resend',
                            style: GoogleFonts.poppins(
                              fontSize: 13,
                              color: _isResending ? AppColor.textHint : AppColor.primary,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ),
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

  Widget _otpBox(BuildContext context, TextEditingController controller) {
    return SizedBox(
      height: 68,
      width: 64,
      child: TextFormField(
        controller: controller,
        onChanged: (value) {
          if (value.length == 1) {
            FocusScope.of(context).nextFocus();
          } else if (value.isEmpty) {
            FocusScope.of(context).previousFocus();
          }
        },
        decoration: InputDecoration(
          filled: true,
          fillColor: Colors.white,
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
          ),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: const BorderSide(color: AppColor.primary, width: 1.5),
          ),
        ),
        style: GoogleFonts.poppins(
          fontSize: 24,
          fontWeight: FontWeight.w700,
          color: AppColor.textPrimary,
        ),
        keyboardType: TextInputType.number,
        textAlign: TextAlign.center,
        inputFormatters: [
          LengthLimitingTextInputFormatter(1),
          FilteringTextInputFormatter.digitsOnly,
        ],
      ),
    );
  }
}
