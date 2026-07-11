import 'dart:io';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import '../../../../core/colors/app_color.dart';
import '../../../../core/network/api_service.dart';
import '../../../../core/network/session_manager.dart';
import '../../../../core/common/widgets/custom_snackbar.dart';
import '../../../home/presentation/controller/home_controller.dart';

class EditProfileScreenView extends StatefulWidget {
  const EditProfileScreenView({super.key});

  @override
  State<EditProfileScreenView> createState() => _EditProfileScreenViewState();
}

class _EditProfileScreenViewState extends State<EditProfileScreenView> {
  final _formKey = GlobalKey<FormState>();
  
  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _phoneCtrl = TextEditingController();
  final _dobCtrl = TextEditingController();
  final _bioCtrl = TextEditingController();
  
  String _selectedGender = 'Male';
  String _selectedLanguage = 'English (EN)';
  bool _isLoading = false;
  
  String? _selectedImagePath;
  String? _existingPhotoUrl;

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  void _loadUserData() {
    final userData = Get.find<HomeController>().userData.value;
    if (userData != null) {
      _nameCtrl.text = userData['name'] ?? '';
      _emailCtrl.text = userData['email'] ?? '';
      _phoneCtrl.text = userData['contact'] ?? userData['phone'] ?? '';
      _dobCtrl.text = userData['dob'] ?? userData['date_of_birth'] ?? '';
      _bioCtrl.text = userData['bio'] ?? '';
      _existingPhotoUrl = userData['photo'];
      
      final gender = userData['gender']?.toString().toLowerCase();
      if (gender != null && (gender == 'male' || gender == 'female' || gender == 'other')) {
        _selectedGender = gender[0].toUpperCase() + gender.substring(1);
      }
    }
  }

  Future<void> _pickImage() async {
    final ImagePicker picker = ImagePicker();
    final XFile? image = await picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      setState(() {
        _selectedImagePath = image.path;
      });
    }
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _emailCtrl.dispose();
    _phoneCtrl.dispose();
    _dobCtrl.dispose();
    _bioCtrl.dispose();
    super.dispose();
  }

  void _saveProfile() async {
    if (!_formKey.currentState!.validate()) return;
    
    setState(() => _isLoading = true);
    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.updateProfile(
        name: _nameCtrl.text.trim(),
        contact: _phoneCtrl.text.trim(),
        dob: _dobCtrl.text.trim(),
        gender: _selectedGender.toLowerCase(),
        bio: _bioCtrl.text.trim(),
        photoPath: _selectedImagePath,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        // Update session
        final updatedUser = response.data['data']['user'];
        if (updatedUser != null) {
           Get.find<HomeController>().updateUserData(updatedUser);
           final token = SessionManager.getToken();
           if (token != null) {
             await SessionManager.saveSession(token, updatedUser);
           }
        }
        CustomSnackbar.showSuccess(
          title: 'Success',
          message: 'Profile updated successfully!',
        );
        Get.back();
      } else {
        CustomSnackbar.showError(message: 'Failed to update profile.');
      }
    } catch (e) {
      CustomSnackbar.showError(message: 'An error occurred while updating profile.');
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColor.background,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, color: AppColor.textPrimary, size: 20),
          onPressed: () => Get.back(),
        ),
        title: Text(
          'Edit Profile',
          style: GoogleFonts.poppins(
            color: AppColor.textPrimary,
            fontWeight: FontWeight.w600,
            fontSize: 16,
          ),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Profile Information',
                style: GoogleFonts.poppins(
                  fontSize: 18,
                  fontWeight: FontWeight.w700,
                  color: AppColor.textPrimary,
                ),
              ),
              const SizedBox(height: 6),
              Text(
                'Update your account\'s profile information and primary identity.',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  color: AppColor.textSecondary,
                  height: 1.4,
                ),
              ),
              const SizedBox(height: 32),

              // Avatar Section
              Center(
                child: Stack(
                  children: [
                    CircleAvatar(
                      radius: 50,
                      backgroundColor: AppColor.primaryLight,
                      backgroundImage: _selectedImagePath != null
                          ? FileImage(File(_selectedImagePath!)) as ImageProvider
                          : (_existingPhotoUrl != null && _existingPhotoUrl!.isNotEmpty
                              ? NetworkImage('${ApiService.mediaBaseUrl}$_existingPhotoUrl', headers: const {'Host': 'bdhandy.test'})
                              : null),
                      child: (_selectedImagePath == null && (_existingPhotoUrl == null || _existingPhotoUrl!.isEmpty))
                          ? Text(
                              _nameCtrl.text.isNotEmpty ? _nameCtrl.text[0].toUpperCase() : 'U',
                              style: GoogleFonts.poppins(
                                color: AppColor.primary,
                                fontSize: 32,
                                fontWeight: FontWeight.w700,
                              ),
                            )
                          : null,
                    ),
                    Positioned(
                      bottom: 0,
                      right: 0,
                      child: GestureDetector(
                        onTap: _pickImage,
                        child: Container(
                          width: 32,
                          height: 32,
                          decoration: BoxDecoration(
                            color: AppColor.primary,
                            shape: BoxShape.circle,
                            border: Border.all(color: Colors.white, width: 2),
                          ),
                          child: const Icon(Icons.camera_alt, size: 16, color: Colors.white),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 32),

              // Name Field (Read Only)
              _buildTextField(
                label: 'Full Name',
                controller: _nameCtrl,
                isReadOnly: true,
                readOnlyText: 'Full Name is Read Only',
              ),
              const SizedBox(height: 20),

              // Email Field (Read Only)
              _buildTextField(
                label: 'Email Address',
                controller: _emailCtrl,
                isReadOnly: true,
                readOnlyText: 'Verified Email - Read Only',
                readOnlyColor: const Color(0xFF16B83E),
              ),
              const SizedBox(height: 20),

              // Phone Field
              _buildTextField(
                label: 'Phone Number',
                controller: _phoneCtrl,
                hint: '+8801...',
              ),
              const SizedBox(height: 20),

              // Gender Dropdown
              _buildDropdownField(
                label: 'Gender',
                value: _selectedGender,
                items: ['Male', 'Female', 'Other'],
                onChanged: (val) {
                  if (val != null) setState(() => _selectedGender = val);
                },
              ),
              const SizedBox(height: 20),

              // DOB Field
              _buildTextField(
                label: 'Date of Birth',
                controller: _dobCtrl,
                hint: 'mm/dd/yyyy',
                suffixIcon: Icons.calendar_today_outlined,
                onTap: () async {
                  final date = await showDatePicker(
                    context: context,
                    initialDate: DateTime.now(),
                    firstDate: DateTime(1900),
                    lastDate: DateTime.now(),
                  );
                  if (date != null) {
                    _dobCtrl.text = "${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}";
                  }
                },
              ),
              const SizedBox(height: 20),

              // Language Dropdown
              _buildDropdownField(
                label: 'Preferred Language',
                value: _selectedLanguage,
                items: ['English (EN)', 'Bengali (BN)'],
                onChanged: (val) {
                  if (val != null) setState(() => _selectedLanguage = val);
                },
              ),
              const SizedBox(height: 20),

              // Short Bio
              _buildTextField(
                label: 'Short Bio',
                controller: _bioCtrl,
                hint: 'Tell us a little about yourself...',
                maxLines: 4,
              ),
              const SizedBox(height: 32),

              // Save Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _saveProfile,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColor.primary,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    elevation: 0,
                  ),
                  child: _isLoading
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                        )
                      : Text(
                          'Save Profile',
                          style: GoogleFonts.poppins(
                            color: Colors.white,
                            fontWeight: FontWeight.w600,
                            fontSize: 15,
                          ),
                        ),
                ),
              ),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildTextField({
    required String label,
    required TextEditingController controller,
    String? hint,
    bool isReadOnly = false,
    String? readOnlyText,
    Color readOnlyColor = AppColor.textHint,
    int maxLines = 1,
    IconData? suffixIcon,
    VoidCallback? onTap,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: GoogleFonts.poppins(
            fontWeight: FontWeight.w500,
            fontSize: 13,
            color: AppColor.textPrimary,
          ),
        ),
        const SizedBox(height: 8),
        TextFormField(
          controller: controller,
          readOnly: isReadOnly || onTap != null,
          onTap: onTap,
          maxLines: maxLines,
          style: GoogleFonts.poppins(color: AppColor.textPrimary, fontSize: 14),
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: GoogleFonts.poppins(fontSize: 13, color: AppColor.textHint),
            suffixIcon: suffixIcon != null ? Icon(suffixIcon, color: AppColor.textSecondary, size: 20) : null,
            filled: true,
            fillColor: isReadOnly ? const Color(0xFFF9FAFB) : Colors.white,
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppColor.primary),
            ),
          ),
        ),
        if (readOnlyText != null) ...[
          const SizedBox(height: 6),
          Row(
            children: [
              Icon(
                readOnlyColor == AppColor.textHint ? Icons.lock_outline : Icons.check,
                size: 12,
                color: readOnlyColor,
              ),
              const SizedBox(width: 4),
              Text(
                readOnlyText,
                style: GoogleFonts.poppins(
                  fontSize: 11,
                  color: readOnlyColor,
                ),
              ),
            ],
          ),
        ],
      ],
    );
  }

  Widget _buildDropdownField({
    required String label,
    required String value,
    required List<String> items,
    required ValueChanged<String?> onChanged,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: GoogleFonts.poppins(
            fontWeight: FontWeight.w500,
            fontSize: 13,
            color: AppColor.textPrimary,
          ),
        ),
        const SizedBox(height: 8),
        DropdownButtonFormField<String>(
          value: value,
          onChanged: onChanged,
          icon: const Icon(Icons.keyboard_arrow_down_rounded, color: AppColor.textSecondary),
          style: GoogleFonts.poppins(color: AppColor.textPrimary, fontSize: 14),
          decoration: InputDecoration(
            filled: true,
            fillColor: Colors.white,
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppColor.primary),
            ),
          ),
          items: items.map((e) => DropdownMenuItem(value: e, child: Text(e))).toList(),
        ),
      ],
    );
  }
}
