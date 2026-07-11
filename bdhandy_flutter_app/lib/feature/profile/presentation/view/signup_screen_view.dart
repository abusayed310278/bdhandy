import 'package:flutter/material.dart';
import 'package:get/get.dart' hide Response;
import 'package:google_fonts/google_fonts.dart';
import 'package:dio/dio.dart';
import '../../../../core/colors/app_color.dart';
import '../../../../core/common/widgets/custom_snackbar.dart';
import '../../../../core/network/api_service.dart';
import 'login_screen_view.dart';
import 'otp_verification_screen_view.dart';

class SignupScreenView extends StatefulWidget {
  const SignupScreenView({super.key});

  @override
  State<SignupScreenView> createState() => _SignupScreenViewState();
}

class _SignupScreenViewState extends State<SignupScreenView> {
  final nameCtrl = TextEditingController();
  final emailCtrl = TextEditingController();
  final phoneCtrl = TextEditingController();
  String _countryCode = '+880';
  final passwordCtrl = TextEditingController();
  final confirmPasswordCtrl = TextEditingController();
  final formKey = GlobalKey<FormState>();
  bool _isLoading = false;

  @override
  void dispose() {
    nameCtrl.dispose();
    emailCtrl.dispose();
    phoneCtrl.dispose();
    passwordCtrl.dispose();
    confirmPasswordCtrl.dispose();
    super.dispose();
  }

  void _signup() async {
    if (formKey.currentState!.validate()) {
      setState(() => _isLoading = true);
      try {
        final apiService = Get.find<ApiService>();
        final response = await apiService.register(
          name: nameCtrl.text.trim(),
          email: emailCtrl.text.trim(),
          password: passwordCtrl.text,
          phoneCountryCode: _countryCode,
          phone: phoneCtrl.text.trim(),
        );
        if ((response.statusCode == 200 || response.statusCode == 201) && response.data != null) {
          final body = response.data;
          if (body['token'] != null || body['user'] != null) {
            final registeredEmail = emailCtrl.text.trim();
            
            nameCtrl.clear();
            emailCtrl.clear();
            phoneCtrl.clear();
            passwordCtrl.clear();
            confirmPasswordCtrl.clear();

            // Go to OTP screen
            Get.to(() => OtpVerificationScreenView(
              email: registeredEmail,
              isRegistration: true,
            ));
            return;
          }
        }
        
        CustomSnackbar.showError(message: 'Registration failed. Please try again.');
      } on DioException catch (e) {
        final body = e.response?.data;
        String errorMsg = 'Registration failed. Please try again.';
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
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Form(
            key: formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Create Account',
                  style: GoogleFonts.poppins(
                    fontSize: 26,
                    fontWeight: FontWeight.w700,
                    color: AppColor.textPrimary,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  'Enter your details to create an account and get started',
                  style: GoogleFonts.poppins(
                    fontSize: 14,
                    color: AppColor.textSecondary,
                    height: 1.5,
                  ),
                ),

                const SizedBox(height: 36),

                // Full Name
                _SignupField(
                  controller: nameCtrl,
                  label: 'Full Name',
                  hint: 'John Doe',
                  icon: Icons.person_outline,
                  obscureText: false,
                  validator: (v) => (v == null || v.isEmpty)
                      ? 'Enter your name'
                      : null,
                ),

                const SizedBox(height: 20),

                // Email
                _SignupField(
                  controller: emailCtrl,
                  label: 'Email Address',
                  hint: 'you@example.com',
                  icon: Icons.email_outlined,
                  obscureText: false,
                  keyboardType: TextInputType.emailAddress,
                  validator: (v) => (v == null || !v.contains('@'))
                      ? 'Enter a valid email'
                      : null,
                ),

                const SizedBox(height: 20),

                // Phone Number
                _PhoneField(
                  controller: phoneCtrl,
                  label: 'Phone number',
                  hint: '01X XXXX XXXX',
                  onCountryCodeChanged: (code) {
                    if (code != null) _countryCode = code;
                  },
                  validator: (v) => (v == null || v.isEmpty)
                      ? 'Enter your phone number'
                      : null,
                ),

                const SizedBox(height: 20),

                // Password
                _SignupField(
                  controller: passwordCtrl,
                  label: 'Password',
                  hint: '••••••••',
                  icon: Icons.lock_outline,
                  obscureText: true,
                  validator: (v) => (v == null || v.length < 8)
                      ? 'Password must be at least 8 characters'
                      : null,
                ),

                const SizedBox(height: 20),

                // Confirm Password
                _SignupField(
                  controller: confirmPasswordCtrl,
                  label: 'Confirm Password',
                  hint: '••••••••',
                  icon: Icons.lock_reset_outlined,
                  obscureText: true,
                  validator: (v) {
                    if (v == null || v.isEmpty) {
                      return 'Confirm your password';
                    }
                    if (v != passwordCtrl.text) {
                      return 'Passwords do not match';
                    }
                    return null;
                  },
                ),

                const SizedBox(height: 36),

                // Signup button
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: _isLoading ? null : _signup,
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
                            'Sign Up',
                            style: GoogleFonts.poppins(
                              color: Colors.white,
                              fontSize: 15,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                  ),
                ),

                const SizedBox(height: 24),

                // Login link
                Center(
                  child: RichText(
                    text: TextSpan(
                      text: 'Already have an account? ',
                      style: GoogleFonts.poppins(
                        fontSize: 13,
                        color: AppColor.textSecondary,
                      ),
                      children: [
                        WidgetSpan(
                          child: GestureDetector(
                            onTap: () => Get.off(() => const LoginScreenView()),
                            child: Text(
                              'Login',
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
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _SignupField extends StatefulWidget {
  final TextEditingController controller;
  final String label;
  final String hint;
  final IconData icon;
  final bool obscureText;
  final TextInputType keyboardType;
  final String? Function(String?)? validator;

  const _SignupField({
    required this.controller,
    required this.label,
    required this.hint,
    required this.icon,
    this.obscureText = false,
    this.keyboardType = TextInputType.text,
    this.validator,
  });

  @override
  State<_SignupField> createState() => _SignupFieldState();
}

class _SignupFieldState extends State<_SignupField> {
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
          style: GoogleFonts.poppins(fontSize: 14, color: AppColor.textPrimary),
          decoration: InputDecoration(
            hintText: widget.hint,
            hintStyle: GoogleFonts.poppins(fontSize: 13, color: AppColor.textHint),
            prefixIcon: Icon(widget.icon, color: AppColor.textSecondary, size: 20),
            suffixIcon: widget.obscureText
                ? GestureDetector(
                    onTap: () => setState(() => _obscure = !_obscure),
                    child: Icon(
                      _obscure ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                      color: AppColor.textSecondary,
                      size: 20,
                    ),
                  )
                : null,
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

class _PhoneField extends StatefulWidget {
  final TextEditingController controller;
  final String label;
  final String hint;
  final String? Function(String?)? validator;
  final void Function(String?) onCountryCodeChanged;

  const _PhoneField({
    required this.controller,
    required this.label,
    required this.hint,
    this.validator,
    required this.onCountryCodeChanged,
  });

  @override
  State<_PhoneField> createState() => _PhoneFieldState();
}

class _PhoneFieldState extends State<_PhoneField> {
  Map<String, String> _selectedCountry = {'iso': 'BD', 'code': '+880', 'name': 'Bangladesh'};
  
  final List<Map<String, String>> _allCountries = [
    {'iso': 'BD', 'code': '+880', 'name': 'Bangladesh'},
    {'iso': 'AE', 'code': '+971', 'name': 'UAE'},
    {'iso': 'SA', 'code': '+966', 'name': 'Saudi Arabia'},
    {'iso': 'QA', 'code': '+974', 'name': 'Qatar'},
    {'iso': 'KW', 'code': '+965', 'name': 'Kuwait'},
    {'iso': 'BH', 'code': '+973', 'name': 'Bahrain'},
    {'iso': 'OM', 'code': '+968', 'name': 'Oman'},
    {'iso': 'IN', 'code': '+91', 'name': 'India'},
    {'iso': 'US', 'code': '+1', 'name': 'USA / Canada'},
    {'iso': 'GB', 'code': '+44', 'name': 'UK'},
    {'iso': 'MY', 'code': '+60', 'name': 'Malaysia'},
    {'iso': 'SG', 'code': '+65', 'name': 'Singapore'},
    {'iso': 'AU', 'code': '+61', 'name': 'Australia'},
    {'iso': 'DE', 'code': '+49', 'name': 'Germany'},
    {'iso': 'FR', 'code': '+33', 'name': 'France'},
    {'iso': 'UZ', 'code': '+998', 'name': 'Uzbekistan'},
  ];

  void _showCountryPicker() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => _CountryPickerSheet(
        countries: _allCountries,
        onSelect: (country) {
          setState(() => _selectedCountry = country);
          widget.onCountryCodeChanged(country['code']);
        },
      ),
    );
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
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            GestureDetector(
              onTap: _showCountryPicker,
              child: Container(
                height: 56,
                padding: const EdgeInsets.symmetric(horizontal: 16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: const Color(0xFFE5E7EB)),
                ),
                child: Row(
                  children: [
                    Text(
                      '${_selectedCountry['iso']} ${_selectedCountry['code']}',
                      style: GoogleFonts.poppins(fontSize: 14, color: AppColor.textPrimary),
                    ),
                    const SizedBox(width: 8),
                    const Icon(Icons.keyboard_arrow_down, size: 18, color: AppColor.textSecondary),
                  ],
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: TextFormField(
                controller: widget.controller,
                keyboardType: TextInputType.phone,
                validator: widget.validator,
                style: GoogleFonts.poppins(fontSize: 14, color: AppColor.textPrimary),
                decoration: InputDecoration(
                  hintText: widget.hint,
                  hintStyle: GoogleFonts.poppins(fontSize: 13, color: AppColor.textHint),
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
            ),
          ],
        ),
        const SizedBox(height: 6),
        Text(
          'Enter number without country code',
          style: GoogleFonts.poppins(fontSize: 12, color: AppColor.textHint),
        ),
      ],
    );
  }
}

class _CountryPickerSheet extends StatefulWidget {
  final List<Map<String, String>> countries;
  final Function(Map<String, String>) onSelect;

  const _CountryPickerSheet({required this.countries, required this.onSelect});

  @override
  State<_CountryPickerSheet> createState() => _CountryPickerSheetState();
}

class _CountryPickerSheetState extends State<_CountryPickerSheet> {
  late List<Map<String, String>> _filtered;
  final TextEditingController _searchCtrl = TextEditingController();

  @override
  void initState() {
    super.initState();
    _filtered = widget.countries;
    _searchCtrl.addListener(() {
      final q = _searchCtrl.text.toLowerCase();
      setState(() {
        _filtered = widget.countries.where((c) => 
          c['name']!.toLowerCase().contains(q) || 
          c['code']!.contains(q) ||
          c['iso']!.toLowerCase().contains(q)
        ).toList();
      });
    });
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      height: MediaQuery.of(context).size.height * 0.7,
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: Column(
        children: [
          const SizedBox(height: 12),
          Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey[300], borderRadius: BorderRadius.circular(2))),
          const SizedBox(height: 16),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: TextField(
              controller: _searchCtrl,
              decoration: InputDecoration(
                hintText: 'Search country...',
                hintStyle: GoogleFonts.poppins(fontSize: 14, color: AppColor.textHint),
                prefixIcon: const Icon(Icons.search, color: AppColor.textSecondary),
                filled: true,
                fillColor: const Color(0xFFF3F4F6),
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
              ),
            ),
          ),
          const SizedBox(height: 8),
          Expanded(
            child: ListView.builder(
              itemCount: _filtered.length,
              itemBuilder: (context, i) {
                final c = _filtered[i];
                return ListTile(
                  title: Row(
                    children: [
                      Text(c['iso']!, style: GoogleFonts.poppins(fontSize: 14, fontWeight: FontWeight.w600, color: AppColor.primary)),
                      const SizedBox(width: 12),
                      Expanded(child: Text(c['name']!, style: GoogleFonts.poppins(fontSize: 14, color: AppColor.textPrimary))),
                      Text(c['code']!, style: GoogleFonts.poppins(fontSize: 14, color: AppColor.textSecondary)),
                    ],
                  ),
                  onTap: () {
                    widget.onSelect(c);
                    Navigator.pop(context);
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
