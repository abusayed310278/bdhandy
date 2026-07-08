import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../../core/colors/app_color.dart';
import '../../../../core/common/widgets/shimmer_placeholder.dart';
import '../../../../core/common/widgets/custom_snackbar.dart';
import '../../../../core/network/api_service.dart';

class ContactScreenView extends StatefulWidget {
  const ContactScreenView({super.key});

  @override
  State<ContactScreenView> createState() => _ContactScreenViewState();
}

class _ContactScreenViewState extends State<ContactScreenView> {
  final _formKey = GlobalKey<FormState>();
  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _messageCtrl = TextEditingController();
  String? _selectedSubject;
  
  bool _isLoading = true;
  bool _isSubmitting = false;
  Map<String, dynamic>? _cmsData;

  @override
  void initState() {
    super.initState();
    _fetchContactData();
  }

  void _fetchContactData() async {
    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.getContactInfo();
      if (response.statusCode == 200 && response.data != null) {
        if (response.data['success'] == true) {
          setState(() {
            _cmsData = response.data['data'];
            _isLoading = false;
          });
          return;
        }
      }
    } catch (_) {}
    setState(() => _isLoading = false);
  }

  Future<void> _submitForm() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSubmitting = true);
    
    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.submitContact(
        name: _nameCtrl.text.trim(),
        email: _emailCtrl.text.trim(),
        subject: _selectedSubject ?? 'General Inquiry',
        message: _messageCtrl.text.trim(),
      );
      
      if (response.statusCode == 200 || response.statusCode == 201) {
        CustomSnackbar.showSuccess(
          title: 'Message Sent',
          message: 'We have received your message and will get back to you shortly.',
        );
        setState(() {
          _selectedSubject = null;
        });
        _nameCtrl.clear();
        _emailCtrl.clear();
        _messageCtrl.clear();
      } else {
        CustomSnackbar.showError(
          message: 'Failed to send message. Please try again.',
        );
      }
    } catch (e) {
      CustomSnackbar.showError(
        message: 'An error occurred. Please check your connection and try again.',
      );
    } finally {
      if (mounted) setState(() => _isSubmitting = false);
    }
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _emailCtrl.dispose();
    _messageCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        backgroundColor: AppColor.background,
        appBar: AppBar(
          backgroundColor: Colors.transparent,
          elevation: 0,
          centerTitle: true,
          title: Text(
            'Contact Us',
            style: GoogleFonts.poppins(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: AppColor.textPrimary,
            ),
          ),
          leading: GestureDetector(
            onTap: () => Get.back(),
            child: const Icon(Icons.arrow_back_ios_new,
                size: 18, color: AppColor.textPrimary),
          ),
        ),
        body: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 24.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const ShimmerPlaceholder.rectangular(height: 32, width: 220),
                const SizedBox(height: 12),
                const ShimmerPlaceholder.rectangular(height: 16, width: double.infinity),
                const SizedBox(height: 6),
                const ShimmerPlaceholder.rectangular(height: 16, width: 180),
                const SizedBox(height: 32),
                
                const ShimmerPlaceholder.rectangular(height: 80, width: double.infinity),
                const SizedBox(height: 16),
                const ShimmerPlaceholder.rectangular(height: 80, width: double.infinity),
                const SizedBox(height: 16),
                const ShimmerPlaceholder.rectangular(height: 80, width: double.infinity),
                const SizedBox(height: 16),
                const ShimmerPlaceholder.rectangular(height: 80, width: double.infinity),
                
                const SizedBox(height: 48),

                const ShimmerPlaceholder.rectangular(height: 24, width: 200),
                const SizedBox(height: 24),
                
                const ShimmerPlaceholder.rectangular(height: 72, width: double.infinity),
                const SizedBox(height: 16),
                const ShimmerPlaceholder.rectangular(height: 72, width: double.infinity),
                const SizedBox(height: 16),
                const ShimmerPlaceholder.rectangular(height: 72, width: double.infinity),
                const SizedBox(height: 16),
                const ShimmerPlaceholder.rectangular(height: 120, width: double.infinity),
                const SizedBox(height: 32),
                const ShimmerPlaceholder.rectangular(height: 56, width: double.infinity),
                const SizedBox(height: 48),
              ],
            ),
          ),
        ),
      );
    }
    
    final title = _cmsData?['title'] ?? 'Get in Touch';
    final subtitle = _cmsData?['subtitle'] ?? 'Have questions? We\'d love to hear from you. Our team is always here to help.';
    
    final officeAddress = _cmsData?['contact_info']?['address'] ?? 'Vancouver, Canada';
    final phoneNumber = _cmsData?['contact_info']?['phone'] ?? '+1 778 804 9199';
    final emailAddress = _cmsData?['contact_info']?['email'] ?? 'info@salojoy.com';
    final workingHours = _cmsData?['contact_info']?['working_hours'] ?? 'Weekdays: 09:00 – 18:00\nWeekend: 10:00 – 15:00';

    return Scaffold(
      backgroundColor: AppColor.background,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        title: Text(
          'Contact Us',
          style: GoogleFonts.poppins(
            fontSize: 18,
            fontWeight: FontWeight.w600,
            color: AppColor.textPrimary,
          ),
        ),
        leading: GestureDetector(
          onTap: () => Get.back(),
          child: const Icon(Icons.arrow_back_ios_new,
              size: 18, color: AppColor.textPrimary),
        ),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 24.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: GoogleFonts.poppins(
                  fontSize: 24,
                  fontWeight: FontWeight.w700,
                  color: AppColor.textPrimary,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                subtitle,
                style: GoogleFonts.poppins(
                  fontSize: 14,
                  color: AppColor.textSecondary,
                  height: 1.5,
                ),
              ),
              const SizedBox(height: 32),
              
              _buildContactCard(
                icon: Icons.location_on_outlined,
                iconColor: const Color(0xFFF97316),
                iconBg: const Color(0xFFFFEDD5),
                title: 'Office Address',
                subtitle: officeAddress,
              ),
              const SizedBox(height: 16),
              
              _buildContactCard(
                icon: Icons.phone_outlined,
                iconColor: const Color(0xFF3B82F6),
                iconBg: const Color(0xFFDBEAFE),
                title: 'Phone Number',
                subtitle: phoneNumber,
              ),
              const SizedBox(height: 16),
              
              _buildContactCard(
                icon: Icons.email_outlined,
                iconColor: const Color(0xFF10B981),
                iconBg: const Color(0xFFD1FAE5),
                title: 'Email Address',
                subtitle: emailAddress,
              ),
              const SizedBox(height: 16),
              
              _buildContactCard(
                icon: Icons.access_time_outlined,
                iconColor: const Color(0xFF8B5CF6),
                iconBg: const Color(0xFFEDE9FE),
                title: 'Working Hours',
                subtitle: workingHours,
              ),
              
              const SizedBox(height: 48),

              // ── Contact Form ──────────────────────────────────────────
              Text(
                'Send us a Message',
                style: GoogleFonts.poppins(
                  fontSize: 20,
                  fontWeight: FontWeight.w700,
                  color: AppColor.textPrimary,
                ),
              ),
              const SizedBox(height: 24),
              
              Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _ContactField(
                      controller: _nameCtrl,
                      label: 'Full Name',
                      hint: 'John Doe',
                      icon: Icons.person_outline,
                      validator: (v) => (v == null || v.isEmpty)
                          ? 'Please enter your name'
                          : null,
                    ),
                    const SizedBox(height: 16),
                    _ContactField(
                      controller: _emailCtrl,
                      label: 'Email Address',
                      hint: 'you@example.com',
                      icon: Icons.email_outlined,
                      keyboardType: TextInputType.emailAddress,
                      validator: (v) => (v == null || !v.contains('@'))
                          ? 'Please enter a valid email'
                          : null,
                    ),
                    const SizedBox(height: 16),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Subject',
                          style: GoogleFonts.poppins(
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            color: AppColor.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 8),
                        DropdownButtonFormField<String>(
                          value: _selectedSubject,
                          icon: const Icon(Icons.keyboard_arrow_down_rounded, color: AppColor.textSecondary),
                          decoration: InputDecoration(
                            hintText: 'Select a subject',
                            hintStyle: GoogleFonts.poppins(fontSize: 13, color: AppColor.textHint),
                            prefixIcon: const Icon(Icons.subject_outlined, color: AppColor.textSecondary, size: 20),
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
                          ),
                          items: (() {
                            final List fallbackSubjects = [
                              {'key': 'general', 'label': 'General Inquiry'},
                              {'key': 'business', 'label': 'Business/Partnership'},
                              {'key': 'support', 'label': 'Technical Support'},
                              {'key': 'billing', 'label': 'Billing & Payments'},
                              {'key': 'other', 'label': 'Other'}
                            ];
                            final List subjectsList = (_cmsData != null && _cmsData!['subjects'] != null)
                                ? _cmsData!['subjects']
                                : fallbackSubjects;
                            return subjectsList.map((dynamic item) {
                              final String key = item['key'] ?? '';
                              final String label = item['label'] ?? '';
                              return DropdownMenuItem<String>(
                                value: key,
                                child: Text(
                                  label,
                                  style: GoogleFonts.poppins(fontSize: 14, color: AppColor.textPrimary),
                                ),
                              );
                            }).toList();
                          })(),
                          onChanged: (String? newValue) {
                            setState(() {
                              _selectedSubject = newValue;
                            });
                          },
                          validator: (v) => (v == null || v.isEmpty) ? 'Please select a subject' : null,
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    _ContactField(
                      controller: _messageCtrl,
                      label: 'Message',
                      hint: 'How can we help you?',
                      icon: Icons.chat_bubble_outline,
                      maxLines: 4,
                      validator: (v) => (v == null || v.isEmpty)
                          ? 'Please enter your message'
                          : null,
                    ),
                    const SizedBox(height: 32),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: _isSubmitting ? null : _submitForm,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColor.primary,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                          elevation: 0,
                        ),
                        child: _isSubmitting
                            ? const SizedBox(
                                height: 20,
                                width: 20,
                                child: CircularProgressIndicator(
                                  color: Colors.white,
                                  strokeWidth: 2,
                                ),
                              )
                            : Text(
                                'Send Message',
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
              const SizedBox(height: 48),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildContactCard({
    required IconData icon,
    required Color iconColor,
    required Color iconBg,
    required String title,
    required String subtitle,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 12,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: iconBg,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: iconColor, size: 24),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 2),
                Text(
                  title,
                  style: GoogleFonts.poppins(
                    fontSize: 15,
                    fontWeight: FontWeight.w600,
                    color: AppColor.textPrimary,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  subtitle,
                  style: GoogleFonts.poppins(
                    fontSize: 13,
                    color: AppColor.textSecondary,
                    height: 1.5,
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

// ─────────────────────────────────────────────────────────────────────────
//  Contact Text Field widget
// ─────────────────────────────────────────────────────────────────────────
class _ContactField extends StatelessWidget {
  final TextEditingController controller;
  final String label;
  final String hint;
  final IconData icon;
  final int maxLines;
  final TextInputType keyboardType;
  final String? Function(String?)? validator;

  const _ContactField({
    required this.controller,
    required this.label,
    required this.hint,
    required this.icon,
    this.maxLines = 1,
    this.keyboardType = TextInputType.text,
    this.validator,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: GoogleFonts.poppins(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            color: AppColor.textPrimary,
          ),
        ),
        const SizedBox(height: 8),
        TextFormField(
          controller: controller,
          keyboardType: keyboardType,
          validator: validator,
          maxLines: maxLines,
          style: GoogleFonts.poppins(fontSize: 14, color: AppColor.textPrimary),
          decoration: InputDecoration(
            hintText: hint,
            hintStyle:
                GoogleFonts.poppins(fontSize: 13, color: AppColor.textHint),
            prefixIcon: maxLines == 1
                ? Icon(icon, color: AppColor.textSecondary, size: 20)
                : Padding(
                    padding: const EdgeInsets.only(bottom: 50),
                    child:
                        Icon(icon, color: AppColor.textSecondary, size: 20),
                  ),
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
