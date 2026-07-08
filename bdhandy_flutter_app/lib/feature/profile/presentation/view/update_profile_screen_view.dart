import 'dart:io';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:file_picker/file_picker.dart';
import '../../../../core/colors/app_color.dart';
import '../../../../core/common/widgets/custom_snackbar.dart';
import '../../../../core/common/widgets/shimmer_placeholder.dart';
import '../../../../core/network/api_service.dart';
import '../../../home/presentation/controller/home_controller.dart';
import 'profile_screen_view.dart';

class UpdateProfileScreenView extends StatefulWidget {
  const UpdateProfileScreenView({super.key});

  @override
  State<UpdateProfileScreenView> createState() =>
      _UpdateProfileScreenViewState();
}

class _UpdateProfileScreenViewState extends State<UpdateProfileScreenView> {
  final _formKey = GlobalKey<FormState>();

  late final TextEditingController _nameController;
  late final TextEditingController _emailController;
  late final TextEditingController _phoneController;
  late final TextEditingController _dobController;
  late final TextEditingController _addressController;

  String? _selectedGender;
  int? _selectedCountryId;
  int? _selectedStateId;
  int? _selectedCityId;

  final List<String> _genders = ['Male', 'Female'];

  bool _isLoading = true;
  bool _isSaving = false;
  String? _localImagePath;

  List<dynamic> _countriesList = [];
  List<dynamic> _statesList = [];
  List<dynamic> _citiesList = [];

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController();
    _emailController = TextEditingController();
    _phoneController = TextEditingController();
    _dobController = TextEditingController();
    _addressController = TextEditingController();

    _fetchProfileAndLocations();
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _dobController.dispose();
    _addressController.dispose();
    super.dispose();
  }

  void _fetchProfileAndLocations() async {
    final apiService = Get.find<ApiService>();

    // 1. Fetch profile details
    try {
      final profileRes = await apiService.getProfile();
      Map<String, dynamic>? user;
      if (profileRes.statusCode == 200 && profileRes.data != null) {
        dynamic data = profileRes.data;
        if (data is String) {
          data = jsonDecode(data);
        }
        if (data['success'] == true) {
          user = data['data']['user'];
          Get.find<HomeController>().updateUserData(user);
        }
      }

      if (user == null) {
        user = Get.find<HomeController>().userData.value;
      }

      if (user != null) {
        _nameController.text = user['name'] ?? '';
        _emailController.text = user['email'] ?? '';
        _phoneController.text = user['contact'] ?? '';
        _dobController.text = user['dob'] ?? '';
        _addressController.text = user['address'] ?? '';

        final String? genderStr = user['gender'];
        if (genderStr != null && genderStr.isNotEmpty) {
          final capitalized = genderStr.capitalizeFirst;
          if (_genders.contains(capitalized)) {
            _selectedGender = capitalized;
          }
        }

        _selectedCountryId = user['country_id'] != null
            ? int.tryParse(user['country_id'].toString())
            : null;
        _selectedStateId = user['state_id'] != null
            ? int.tryParse(user['state_id'].toString())
            : null;
        _selectedCityId = user['city_id'] != null
            ? int.tryParse(user['city_id'].toString())
            : null;
      }
    } catch (e) {
      print("Error fetching profile details: $e");
      CustomSnackbar.showError(message: 'Profile API error: $e');
    }

    // 2. Fetch Countries list
    try {
      final countriesRes = await apiService.getCountries();
      if (countriesRes.statusCode == 200 && countriesRes.data != null) {
        dynamic data = countriesRes.data;
        if (data is String) {
          data = jsonDecode(data);
        }
        if (data['success'] == true) {
          _countriesList = data['data']['countries'] ?? [];
        } else {
          CustomSnackbar.showError(message: 'Countries API success was false');
        }
      } else {
        CustomSnackbar.showError(
          message: 'Countries API returned status: ${countriesRes.statusCode}',
        );
      }
    } catch (e) {
      print("Error fetching countries list: $e");
      CustomSnackbar.showError(message: 'Countries API error: $e');
    }

    // 3. Fetch States if country_id is selected
    if (_selectedCountryId != null) {
      try {
        final statesRes = await apiService.getStates(_selectedCountryId!);
        if (statesRes.statusCode == 200 && statesRes.data != null) {
          dynamic data = statesRes.data;
          if (data is String) {
            data = jsonDecode(data);
          }
          _statesList = data;
        }
      } catch (e) {
        print("Error fetching states: $e");
        CustomSnackbar.showError(message: 'States API error: $e');
      }
    }

    // 4. Fetch Cities if state_id is selected
    if (_selectedStateId != null) {
      try {
        final citiesRes = await apiService.getCities(_selectedStateId!);
        if (citiesRes.statusCode == 200 && citiesRes.data != null) {
          dynamic data = citiesRes.data;
          if (data is String) {
            data = jsonDecode(data);
          }
          _citiesList = data;
        }
      } catch (e) {
        print("Error fetching cities: $e");
        CustomSnackbar.showError(message: 'Cities API error: $e');
      }
    }

    if (mounted) {
      setState(() => _isLoading = false);
    }
  }

  void _onCountryChanged(int? val) async {
    if (val == null) return;
    setState(() {
      _selectedCountryId = val;
      _selectedStateId = null;
      _selectedCityId = null;
      _statesList = [];
      _citiesList = [];
    });
    try {
      final res = await Get.find<ApiService>().getStates(val);
      if (res.statusCode == 200 && res.data != null) {
        dynamic data = res.data;
        if (data is String) {
          data = jsonDecode(data);
        }
        setState(() {
          _statesList = data;
        });
      } else {
        CustomSnackbar.showError(
          message: 'States API returned status: ${res.statusCode}',
        );
      }
    } catch (e) {
      CustomSnackbar.showError(message: 'States API error: $e');
    }
  }

  void _onStateChanged(int? val) async {
    if (val == null) return;
    setState(() {
      _selectedStateId = val;
      _selectedCityId = null;
      _citiesList = [];
    });
    try {
      final res = await Get.find<ApiService>().getCities(val);
      if (res.statusCode == 200 && res.data != null) {
        dynamic data = res.data;
        if (data is String) {
          data = jsonDecode(data);
        }
        setState(() {
          _citiesList = data;
        });
      } else {
        CustomSnackbar.showError(
          message: 'Cities API returned status: ${res.statusCode}',
        );
      }
    } catch (e) {
      CustomSnackbar.showError(message: 'Cities API error: $e');
    }
  }

  Future<void> _pickImage() async {
    // try {
    //   final FilePickerResult? result = await FilePicker.platform.pickFiles(
    //     type: FileType.custom,
    //     allowedExtensions: ['jpg', 'png', 'jpeg'],
    //   );

    //   if (result != null && result.files.single.path != null) {
    //     final path = result.files.single.path!;
    //     final file = File(path);

    //     // Validate size (Max 5MB)
    //     final int sizeInBytes = await file.length();
    //     final double sizeInMb = sizeInBytes / (1024 * 1024);
    //     if (sizeInMb > 5.0) {
    //       CustomSnackbar.showError(message: 'File size must be under 5MB.');
    //       return;
    //     }

    //     setState(() {
    //       _localImagePath = path;
    //     });
    //   }
    // } catch (e) {
    //   print('Error picking image: $e');
    // }
  }

  Future<void> _selectDate(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime(1995, 8, 15),
      firstDate: DateTime(1900),
      lastDate: DateTime.now(),
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: AppColor.primary,
              onPrimary: Colors.white,
              onSurface: AppColor.textPrimary,
            ),
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      setState(() {
        _dobController.text =
            "${picked.year}-${picked.month.toString().padLeft(2, '0')}-${picked.day.toString().padLeft(2, '0')}";
      });
    }
  }

  void _saveProfile() async {
    if (!_formKey.currentState!.validate()) return;

    if (_nameController.text.trim().isEmpty) {
      CustomSnackbar.showError(message: 'Name is required.');
      return;
    }

    setState(() => _isSaving = true);
    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.updateProfile(
        name: _nameController.text.trim(),
        contact: _phoneController.text.trim().isEmpty
            ? null
            : _phoneController.text.trim(),
        dob: _dobController.text.trim().isEmpty
            ? null
            : _dobController.text.trim(),
        gender: _selectedGender?.toLowerCase(),
        address: _addressController.text.trim().isEmpty
            ? null
            : _addressController.text.trim(),
        countryId: _selectedCountryId,
        stateId: _selectedStateId,
        cityId: _selectedCityId,
        photoPath: _localImagePath,
      );

      if (response.statusCode == 200 && response.data != null) {
        dynamic data = response.data;
        if (data is String) {
          data = jsonDecode(data);
        }
        if (data['success'] == true) {
          final updatedUser = data['data']['user'];

          // Save and propagate new session data instantly
          Get.find<HomeController>().updateUserData(updatedUser);
          ProfileScreenView.userData = updatedUser;

          Get.back();
          CustomSnackbar.showSuccess(
            title: 'Profile Updated',
            message: 'Your profile details have been saved.',
          );
          return;
        }
      }
      CustomSnackbar.showError(
        message: 'Failed to update profile. Please try again.',
      );
    } catch (e) {
      print("Error saving profile: $e");
      CustomSnackbar.showError(
        message: 'An error occurred while updating profile.',
      );
    } finally {
      if (mounted) {
        setState(() => _isSaving = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        backgroundColor: AppColor.background,
        appBar: AppBar(
          backgroundColor: Colors.white,
          elevation: 0,
          leading: IconButton(
            icon: const Icon(
              Icons.arrow_back_ios_new,
              color: AppColor.textPrimary,
              size: 20,
            ),
            onPressed: () => Get.back(),
          ),
          title: Text(
            'Update Profile',
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
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: ShimmerPlaceholder.circular(width: 100, height: 100),
              ),
              const SizedBox(height: 32),
              ShimmerPlaceholder.rectangular(height: 18),
              const SizedBox(height: 12),
              ShimmerPlaceholder.rectangular(height: 50),
              const SizedBox(height: 20),
              ShimmerPlaceholder.rectangular(height: 18),
              const SizedBox(height: 12),
              ShimmerPlaceholder.rectangular(height: 50),
              const SizedBox(height: 20),
              ShimmerPlaceholder.rectangular(height: 18),
              const SizedBox(height: 12),
              ShimmerPlaceholder.rectangular(height: 50),
              const SizedBox(height: 32),
              ShimmerPlaceholder.rectangular(height: 18),
              const SizedBox(height: 12),
              ShimmerPlaceholder.rectangular(height: 50),
            ],
          ),
        ),
      );
    }

    final String? userPhoto =
        Get.find<HomeController>().userData.value?['photo'];

    return Scaffold(
      backgroundColor: AppColor.background,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(
            Icons.arrow_back_ios_new,
            color: AppColor.textPrimary,
            size: 20,
          ),
          onPressed: () => Get.back(),
        ),
        title: Text(
          'Update Profile',
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
              // Avatar Section
              Center(
                child: Stack(
                  children: [
                    GestureDetector(
                      onTap: _pickImage,
                      child: CircleAvatar(
                        radius: 50,
                        backgroundColor: AppColor.primaryLight,
                        child: (_localImagePath != null)
                            ? ClipOval(
                                child: Image.file(
                                  File(_localImagePath!),
                                  width: 100,
                                  height: 100,
                                  fit: BoxFit.cover,
                                ),
                              )
                            : (userPhoto != null && userPhoto.isNotEmpty)
                            ? ClipOval(
                                child: Image.network(
                                  '${ApiService.mediaBaseUrl}$userPhoto',
                                  width: 100,
                                  height: 100,
                                  fit: BoxFit.cover,
                                  errorBuilder: (_, __, ___) => Text(
                                    (() {
                                      final name =
                                          Get.find<HomeController>()
                                              .userData
                                              .value?['name'] ??
                                          'User';
                                      final parts = name.split(' ');
                                      String initials = parts
                                          .map((p) => p.isNotEmpty ? p[0] : '')
                                          .join('')
                                          .toUpperCase();
                                      if (initials.length > 2)
                                        initials = initials.substring(0, 2);
                                      if (initials.isEmpty) initials = 'U';
                                      return initials;
                                    })(),
                                    style: GoogleFonts.poppins(
                                      color: AppColor.primary,
                                      fontSize: 32,
                                      fontWeight: FontWeight.w700,
                                    ),
                                  ),
                                ),
                              )
                            : Text(
                                (() {
                                  final name =
                                      Get.find<HomeController>()
                                          .userData
                                          .value?['name'] ??
                                      'User';
                                  final parts = name.split(' ');
                                  String initials = parts
                                      .map((p) => p.isNotEmpty ? p[0] : '')
                                      .join('')
                                      .toUpperCase();
                                  if (initials.length > 2)
                                    initials = initials.substring(0, 2);
                                  if (initials.isEmpty) initials = 'U';
                                  return initials;
                                })(),
                                style: GoogleFonts.poppins(
                                  color: AppColor.primary,
                                  fontSize: 32,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                      ),
                    ),
                    Positioned(
                      bottom: 0,
                      right: 0,
                      child: GestureDetector(
                        onTap: _pickImage,
                        child: Container(
                          padding: const EdgeInsets.all(8),
                          decoration: const BoxDecoration(
                            color: AppColor.primary,
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.camera_alt,
                            color: Colors.white,
                            size: 18,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 32),

              // Section: Account Information
              _buildSectionHeader(
                'Account Information',
                Icons.account_circle_outlined,
              ),
              const SizedBox(height: 16),
              _buildTextField(
                label: 'Full Name',
                controller: _nameController,
                icon: Icons.person_outline,
                isReadonly: false,
              ),
              const SizedBox(height: 20),
              _buildTextField(
                label: 'Email Address',
                controller: _emailController,
                icon: Icons.email_outlined,
                isReadonly: true,
                subtitle: 'non-editable',
              ),
              const SizedBox(height: 20),
              _buildTextField(
                label: 'Contact',
                controller: _phoneController,
                icon: Icons.phone_outlined,
                keyboardType: TextInputType.phone,
              ),
              const SizedBox(height: 32),

              // Section: Personal Details
              _buildSectionHeader('Personal Details', Icons.badge_outlined),
              const SizedBox(height: 16),
              // Date of Birth
              Text(
                'Date of Birth',
                style: GoogleFonts.poppins(
                  fontWeight: FontWeight.w600,
                  color: AppColor.textPrimary,
                ),
              ),
              const SizedBox(height: 8),
              GestureDetector(
                onTap: () => _selectDate(context),
                child: AbsorbPointer(
                  child: TextField(
                    controller: _dobController,
                    style: GoogleFonts.poppins(
                      color: AppColor.textPrimary,
                      fontSize: 14,
                    ),
                    decoration: InputDecoration(
                      prefixIcon: const Icon(
                        Icons.calendar_today_outlined,
                        color: AppColor.textSecondary,
                        size: 20,
                      ),
                      filled: true,
                      fillColor: Colors.white,
                      contentPadding: const EdgeInsets.symmetric(
                        horizontal: 16,
                        vertical: 16,
                      ),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
                      ),
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 20),
              // Gender Selectors (Selectable modern segment pills)
              Text(
                'Gender',
                style: GoogleFonts.poppins(
                  fontWeight: FontWeight.w600,
                  color: AppColor.textPrimary,
                ),
              ),
              const SizedBox(height: 8),
              Row(
                children: _genders.map((gender) {
                  final bool isSelected = _selectedGender == gender;
                  return Expanded(
                    child: GestureDetector(
                      onTap: () {
                        setState(() {
                          _selectedGender = gender;
                        });
                      },
                      child: Container(
                        margin: EdgeInsets.only(
                          right: gender == _genders.first ? 8.0 : 0.0,
                          left: gender == _genders.last ? 8.0 : 0.0,
                        ),
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        decoration: BoxDecoration(
                          color: isSelected
                              ? AppColor.primary.withOpacity(0.08)
                              : Colors.white,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: isSelected
                                ? AppColor.primary
                                : const Color(0xFFE5E7EB),
                            width: isSelected ? 1.5 : 1.0,
                          ),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              gender == 'Male' ? Icons.male : Icons.female,
                              color: isSelected
                                  ? AppColor.primary
                                  : AppColor.textSecondary,
                              size: 20,
                            ),
                            const SizedBox(width: 8),
                            Text(
                              gender,
                              style: GoogleFonts.poppins(
                                color: isSelected
                                    ? AppColor.primary
                                    : AppColor.textPrimary,
                                fontWeight: isSelected
                                    ? FontWeight.w600
                                    : FontWeight.w500,
                                fontSize: 14,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  );
                }).toList(),
              ),
              const SizedBox(height: 20),
              // Address
              _buildTextField(
                label: 'Address',
                controller: _addressController,
                icon: Icons.home_outlined,
                maxLines: 3,
              ),
              const SizedBox(height: 32),

              // Section: Location
              _buildSectionHeader('Location', Icons.location_on_outlined),
              const SizedBox(height: 16),
              // Country Dropdown
              Text(
                'Country',
                style: GoogleFonts.poppins(
                  fontWeight: FontWeight.w600,
                  color: AppColor.textPrimary,
                ),
              ),
              const SizedBox(height: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFFE5E7EB)),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<int>(
                    value:
                        _countriesList.any(
                          (c) =>
                              int.parse(c['id'].toString()) ==
                              _selectedCountryId,
                        )
                        ? _selectedCountryId
                        : null,
                    isExpanded: true,
                    hint: Text(
                      'Select Country',
                      style: GoogleFonts.poppins(color: AppColor.textHint),
                    ),
                    icon: const Icon(
                      Icons.keyboard_arrow_down,
                      color: AppColor.textSecondary,
                    ),
                    style: GoogleFonts.poppins(
                      color: AppColor.textPrimary,
                      fontSize: 14,
                    ),
                    onChanged: _onCountryChanged,
                    items: _countriesList.map((country) {
                      final int id = int.parse(country['id'].toString());
                      final String name = country['name'] ?? '';
                      return DropdownMenuItem<int>(
                        value: id,
                        child: Text(name),
                      );
                    }).toList(),
                  ),
                ),
              ),
              const SizedBox(height: 20),
              // State Dropdown
              Text(
                'State',
                style: GoogleFonts.poppins(
                  fontWeight: FontWeight.w600,
                  color: AppColor.textPrimary,
                ),
              ),
              const SizedBox(height: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFFE5E7EB)),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<int>(
                    value:
                        _statesList.any(
                          (s) =>
                              int.parse(s['id'].toString()) == _selectedStateId,
                        )
                        ? _selectedStateId
                        : null,
                    isExpanded: true,
                    hint: Text(
                      'Select State',
                      style: GoogleFonts.poppins(color: AppColor.textHint),
                    ),
                    icon: const Icon(
                      Icons.keyboard_arrow_down,
                      color: AppColor.textSecondary,
                    ),
                    style: GoogleFonts.poppins(
                      color: AppColor.textPrimary,
                      fontSize: 14,
                    ),
                    onChanged: _onStateChanged,
                    items: _statesList.map((state) {
                      final int id = int.parse(state['id'].toString());
                      final String name = state['name'] ?? '';
                      return DropdownMenuItem<int>(
                        value: id,
                        child: Text(name),
                      );
                    }).toList(),
                  ),
                ),
              ),
              const SizedBox(height: 20),
              // City Dropdown
              Text(
                'City',
                style: GoogleFonts.poppins(
                  fontWeight: FontWeight.w600,
                  color: AppColor.textPrimary,
                ),
              ),
              const SizedBox(height: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFFE5E7EB)),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<int>(
                    value:
                        _citiesList.any(
                          (c) =>
                              int.parse(c['id'].toString()) == _selectedCityId,
                        )
                        ? _selectedCityId
                        : null,
                    isExpanded: true,
                    hint: Text(
                      'Select City',
                      style: GoogleFonts.poppins(color: AppColor.textHint),
                    ),
                    icon: const Icon(
                      Icons.keyboard_arrow_down,
                      color: AppColor.textSecondary,
                    ),
                    style: GoogleFonts.poppins(
                      color: AppColor.textPrimary,
                      fontSize: 14,
                    ),
                    onChanged: (val) {
                      setState(() => _selectedCityId = val);
                    },
                    items: _citiesList.map((city) {
                      final int id = int.parse(city['id'].toString());
                      final String name = city['name'] ?? '';
                      return DropdownMenuItem<int>(
                        value: id,
                        child: Text(name),
                      );
                    }).toList(),
                  ),
                ),
              ),
              const SizedBox(height: 40),

              // Save Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _isSaving ? null : _saveProfile,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColor.primary,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    elevation: 0,
                  ),
                  child: _isSaving
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            color: Colors.white,
                          ),
                        )
                      : Text(
                          'Save Changes',
                          style: GoogleFonts.poppins(
                            color: Colors.white,
                            fontWeight: FontWeight.w600,
                            fontSize: 16,
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

  Widget _buildSectionHeader(String title, IconData icon) {
    return Row(
      children: [
        Icon(icon, color: AppColor.primary, size: 20),
        const SizedBox(width: 8),
        Text(
          title,
          style: GoogleFonts.poppins(
            fontSize: 14,
            fontWeight: FontWeight.w700,
            color: AppColor.textPrimary,
          ),
        ),
      ],
    );
  }

  Widget _buildTextField({
    required String label,
    required TextEditingController controller,
    required IconData icon,
    TextInputType? keyboardType,
    bool isReadonly = false,
    String? subtitle,
    int maxLines = 1,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(
              label,
              style: GoogleFonts.poppins(
                fontWeight: FontWeight.w600,
                color: AppColor.textPrimary,
              ),
            ),
            if (subtitle != null) ...[
              const SizedBox(width: 6),
              Text(
                '($subtitle)',
                style: GoogleFonts.poppins(
                  fontSize: 11,
                  fontWeight: FontWeight.w500,
                  color: AppColor.textHint,
                ),
              ),
            ],
          ],
        ),
        const SizedBox(height: 8),
        TextField(
          controller: controller,
          keyboardType: keyboardType,
          readOnly: isReadonly,
          maxLines: maxLines,
          style: GoogleFonts.poppins(
            color: isReadonly ? AppColor.textHint : AppColor.textPrimary,
            fontSize: 14,
          ),
          decoration: InputDecoration(
            prefixIcon: Icon(icon, color: AppColor.textSecondary, size: 20),
            filled: true,
            fillColor: isReadonly ? const Color(0xFFF9FAFB) : Colors.white,
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 16,
              vertical: 16,
            ),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide(
                color: isReadonly
                    ? const Color(0xFFE5E7EB)
                    : const Color(0xFFE5E7EB),
              ),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: Color(0xFFE5E7EB)),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide(
                color: isReadonly ? const Color(0xFFE5E7EB) : AppColor.primary,
              ),
            ),
          ),
        ),
      ],
    );
  }
}
