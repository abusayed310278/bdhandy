import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../../../core/network/api_service.dart';
import '../../../../feature/profile/presentation/view/my_addresses_screen.dart';

class AddAddressScreen extends StatefulWidget {
  const AddAddressScreen({super.key});

  @override
  State<AddAddressScreen> createState() => _AddAddressScreenState();
}

class _AddAddressScreenState extends State<AddAddressScreen> {
  final _formKey = GlobalKey<FormState>();

  final labelController = TextEditingController();
  final addressController = TextEditingController();

  String selectedType = 'House / Home';
  String? selectedCountry;
  String? selectedDivision;
  String? selectedDistrict;
  String? selectedArea;

  @override
  void dispose() {
    labelController.dispose();
    addressController.dispose();
    super.dispose();
  }

  bool _isSubmitting = false;

  Future<void> _submitAddress() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isSubmitting = true);

    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.addAddress({
         'label': labelController.text.trim(),
         'address_type': selectedType,
         'address': addressController.text.trim(),
         'is_primary': false,
      });

      if (response.statusCode == 200 && response.data['success'] == true) {
        Get.snackbar(
          'Success',
          'Address added successfully',
          snackPosition: SnackPosition.TOP,
          backgroundColor: Colors.green,
          colorText: Colors.white,
        );
        Get.off(() => const MyAddressesScreen());
      }
    } catch (e) {
        Get.snackbar(
          'Error',
          'Failed to add address',
          snackPosition: SnackPosition.TOP,
          backgroundColor: Colors.red,
          colorText: Colors.white,
        );
    } finally {
      setState(() => _isSubmitting = false);
    }
  }

  InputDecoration _inputDecoration({
    required String hint,
    Widget? prefixIcon,
  }) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(
        color: Color(0xff94A3B8),
        fontSize: 14,
        fontWeight: FontWeight.w500,
      ),
      prefixIcon: prefixIcon,
      filled: true,
      fillColor: const Color(0xffF8FAFC),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16),
        borderSide: const BorderSide(color: Color(0xffE2E8F0)),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16),
        borderSide: const BorderSide(color: Color(0xff1D9BF0), width: 1.4),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16),
        borderSide: const BorderSide(color: Colors.redAccent),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16),
        borderSide: const BorderSide(color: Colors.redAccent),
      ),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16),
      ),
    );
  }

  Widget _label(String text, {bool required = false}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        children: [
          Text(
            text,
            style: const TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.w800,
              color: Color(0xff0F172A),
            ),
          ),
          if (required)
            const Text(
              ' *',
              style: TextStyle(
                color: Colors.red,
                fontWeight: FontWeight.bold,
              ),
            ),
        ],
      ),
    );
  }

  Widget _sectionCard({
    required String title,
    required IconData icon,
    required List<Widget> children,
  }) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        border: Border.all(color: const Color(0xffE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.035),
            blurRadius: 18,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: const Color(0xffEAF5FF),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(
                  icon,
                  color: const Color(0xff1D9BF0),
                  size: 22,
                ),
              ),
              const SizedBox(width: 12),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 17,
                  fontWeight: FontWeight.w900,
                  color: Color(0xff0F172A),
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          ...children,
        ],
      ),
    );
  }

  Widget _dropdownField({
    required String? value,
    required String hint,
    required List<String> items,
    required ValueChanged<String?> onChanged,
    String? Function(String?)? validator,
  }) {
    return DropdownButtonFormField<String>(
      value: value,
      isExpanded: true,
      icon: const Icon(
        Icons.keyboard_arrow_down_rounded,
        color: Color(0xff334155),
      ),
      decoration: _inputDecoration(hint: hint),
      style: const TextStyle(
        fontSize: 14,
        color: Color(0xff0F172A),
        fontWeight: FontWeight.w600,
      ),
      items: items
          .map(
            (item) => DropdownMenuItem<String>(
              value: item,
              child: Text(item),
            ),
          )
          .toList(),
      onChanged: onChanged,
      validator: validator,
    );
  }

  Widget _buildMapPreview() {
    return Container(
      height: 185,
      width: double.infinity,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(18),
        color: const Color(0xffEAF5FF),
        border: Border.all(color: const Color(0xffDCEAF7)),
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(18),
        child: Stack(
          children: [
            Positioned.fill(
              child: CustomPaint(
                painter: _MapPatternPainter(),
              ),
            ),

            Center(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      shape: BoxShape.circle,
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.12),
                          blurRadius: 18,
                          offset: const Offset(0, 8),
                        ),
                      ],
                    ),
                    child: const Icon(
                      Icons.location_on,
                      color: Color(0xffEF4444),
                      size: 34,
                    ),
                  ),
                  const SizedBox(height: 10),
                  const Text(
                    'Dhaka',
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.w900,
                      color: Color(0xff0F172A),
                    ),
                  ),
                ],
              ),
            ),

            Positioned(
              right: 12,
              bottom: 12,
              child: Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(14),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.08),
                      blurRadius: 12,
                    ),
                  ],
                ),
                child: const Icon(
                  Icons.my_location,
                  color: Color(0xff1D9BF0),
                  size: 21,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLocationActions() {
    return Row(
      children: [
        Expanded(
          child: OutlinedButton.icon(
            onPressed: () {},
            icon: const Icon(Icons.my_location, size: 18),
            label: const Text('Use Location'),
            style: OutlinedButton.styleFrom(
              minimumSize: const Size.fromHeight(48),
              foregroundColor: const Color(0xff1D9BF0),
              side: const BorderSide(color: Color(0xffD7E7F5)),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(15),
              ),
            ),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: OutlinedButton.icon(
            onPressed: () {},
            icon: const Icon(Icons.map_outlined, size: 18),
            label: const Text('Set on Map'),
            style: OutlinedButton.styleFrom(
              minimumSize: const Size.fromHeight(48),
              foregroundColor: const Color(0xff334155),
              side: const BorderSide(color: Color(0xffD7E7F5)),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(15),
              ),
            ),
          ),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xffF8FAFC),
      appBar: AppBar(
        elevation: 0,
        backgroundColor: const Color(0xffF8FAFC),
        surfaceTintColor: const Color(0xffF8FAFC),
        leading: IconButton(
          onPressed: () => Get.back(),
          icon: const Icon(
            Icons.arrow_back_ios_new,
            color: Color(0xff0F172A),
            size: 20,
          ),
        ),
        titleSpacing: 0,
        title: const Text(
          'My Addresses',
          style: TextStyle(
            color: Color(0xff64748B),
            fontSize: 15,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),

      bottomNavigationBar: SafeArea(
        top: false,
        child: Container(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 16),
          decoration: BoxDecoration(
            color: Colors.white,
            border: Border(
              top: BorderSide(color: Colors.grey.shade200),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.04),
                blurRadius: 18,
                offset: const Offset(0, -6),
              ),
            ],
          ),
          child: Row(
            children: [
              Expanded(
                child: OutlinedButton(
                  onPressed: () => Get.back(),
                  style: OutlinedButton.styleFrom(
                    minimumSize: const Size.fromHeight(54),
                    side: const BorderSide(color: Color(0xffD0DCE8)),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                  ),
                  child: const Text(
                    'Cancel',
                    style: TextStyle(
                      color: Color(0xff475569),
                      fontSize: 15,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                flex: 2,
                child: ElevatedButton(
                  onPressed: _isSubmitting ? null : _submitAddress,
                  style: ElevatedButton.styleFrom(
                    minimumSize: const Size.fromHeight(54),
                    backgroundColor: const Color(0xff1D9BF0),
                    foregroundColor: Colors.white,
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                  ),
                  child: _isSubmitting
                    ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                    : const Text(
                    'Add Address',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),

      body: SafeArea(
        child: Form(
          key: _formKey,
          child: SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(16, 4, 16, 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Add New Address',
                  style: TextStyle(
                    fontSize: 28,
                    fontWeight: FontWeight.w900,
                    color: Color(0xff0F172A),
                    height: 1.15,
                  ),
                ),
                const SizedBox(height: 8),
                const Text(
                  'Save your address to make future bookings faster and easier.',
                  style: TextStyle(
                    fontSize: 14,
                    color: Color(0xff64748B),
                    height: 1.4,
                  ),
                ),

                const SizedBox(height: 20),

                _sectionCard(
                  title: 'Address Info',
                  icon: Icons.home_work_outlined,
                  children: [
                    _label('Label', required: true),
                    TextFormField(
                      controller: labelController,
                      decoration: _inputDecoration(
                        hint: 'e.g. Home, My Office',
                        prefixIcon: const Icon(
                          Icons.label_outline,
                          color: Color(0xff94A3B8),
                        ),
                      ),
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Please enter address label';
                        }
                        return null;
                      },
                    ),

                    const SizedBox(height: 16),

                    _label('Type', required: true),
                    _dropdownField(
                      value: selectedType,
                      hint: 'Select type',
                      items: const [
                        'House / Home',
                        'Apartment',
                        'Office',
                        'Shop',
                        'Other',
                      ],
                      onChanged: (value) {
                        setState(() {
                          selectedType = value!;
                        });
                      },
                    ),

                    const SizedBox(height: 16),

                    _label('Full Address', required: true),
                    TextFormField(
                      controller: addressController,
                      minLines: 1,
                      maxLines: 3,
                      decoration: _inputDecoration(
                        hint: 'Search for your address or type it',
                        prefixIcon: const Icon(
                          Icons.search,
                          color: Color(0xff94A3B8),
                        ),
                      ),
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Please enter full address';
                        }
                        return null;
                      },
                    ),

                    const SizedBox(height: 16),

                    _buildMapPreview(),

                    const SizedBox(height: 12),

                    const Text(
                      'Search address above or drag the pin to set exact coordinates.',
                      style: TextStyle(
                        fontSize: 12,
                        color: Color(0xff64748B),
                        height: 1.4,
                      ),
                    ),

                    const SizedBox(height: 16),

                    _buildLocationActions(),
                  ],
                ),

                const SizedBox(height: 18),

                _sectionCard(
                  title: 'Region',
                  icon: Icons.public_outlined,
                  children: [
                    _label('Country', required: true),
                    _dropdownField(
                      value: selectedCountry,
                      hint: 'Select country',
                      items: const [
                        'Bangladesh',
                        'United Arab Emirates',
                        'India',
                        'Pakistan',
                      ],
                      onChanged: (value) {
                        setState(() {
                          selectedCountry = value;
                        });
                      },
                      validator: (value) {
                        if (value == null) return 'Please select country';
                        return null;
                      },
                    ),

                    const SizedBox(height: 16),

                    _label('Division', required: true),
                    _dropdownField(
                      value: selectedDivision,
                      hint: 'Select division',
                      items: const [
                        'Dhaka',
                        'Chattogram',
                        'Rajshahi',
                        'Khulna',
                        'Sylhet',
                      ],
                      onChanged: (value) {
                        setState(() {
                          selectedDivision = value;
                        });
                      },
                      validator: (value) {
                        if (value == null) return 'Please select division';
                        return null;
                      },
                    ),

                    const SizedBox(height: 16),

                    _label('District', required: true),
                    _dropdownField(
                      value: selectedDistrict,
                      hint: 'Select district',
                      items: const [
                        'Dhaka',
                        'Gazipur',
                        'Narayanganj',
                        'Cumilla',
                        'Chattogram',
                      ],
                      onChanged: (value) {
                        setState(() {
                          selectedDistrict = value;
                        });
                      },
                      validator: (value) {
                        if (value == null) return 'Please select district';
                        return null;
                      },
                    ),

                    const SizedBox(height: 16),

                    _label('Area / Upazila'),
                    _dropdownField(
                      value: selectedArea,
                      hint: 'Select area',
                      items: const [
                        'Mirpur',
                        'Dhanmondi',
                        'Uttara',
                        'Gulshan',
                        'Banani',
                        'Bashundhara',
                      ],
                      onChanged: (value) {
                        setState(() {
                          selectedArea = value;
                        });
                      },
                    ),
                  ],
                ),

                const SizedBox(height: 90),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _MapPatternPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final roadPaint = Paint()
      ..color = Colors.white.withOpacity(0.9)
      ..strokeWidth = 9
      ..strokeCap = StrokeCap.round;

    final roadBorderPaint = Paint()
      ..color = const Color(0xffCFE7F9)
      ..strokeWidth = 12
      ..strokeCap = StrokeCap.round;

    final riverPaint = Paint()
      ..color = const Color(0xffBAE6FD).withOpacity(0.9)
      ..strokeWidth = 24
      ..strokeCap = StrokeCap.round;

    final greenPaint = Paint()
      ..color = const Color(0xffBBF7D0).withOpacity(0.55)
      ..style = PaintingStyle.fill;

    canvas.drawCircle(Offset(size.width * .12, size.height * .18), 70, greenPaint);
    canvas.drawCircle(Offset(size.width * .86, size.height * .80), 80, greenPaint);

    final river = Path()
      ..moveTo(-20, size.height * .75)
      ..quadraticBezierTo(size.width * .25, size.height * .45, size.width * .5, size.height * .62)
      ..quadraticBezierTo(size.width * .72, size.height * .77, size.width + 20, size.height * .48);

    canvas.drawPath(river, riverPaint);

    final road1 = Path()
      ..moveTo(-10, size.height * .25)
      ..quadraticBezierTo(size.width * .28, size.height * .35, size.width * .55, size.height * .25)
      ..quadraticBezierTo(size.width * .75, size.height * .18, size.width + 20, size.height * .3);

    canvas.drawPath(road1, roadBorderPaint);
    canvas.drawPath(road1, roadPaint);

    final road2 = Path()
      ..moveTo(size.width * .18, -10)
      ..quadraticBezierTo(size.width * .35, size.height * .35, size.width * .32, size.height + 10);

    canvas.drawPath(road2, roadBorderPaint);
    canvas.drawPath(road2, roadPaint);

    final road3 = Path()
      ..moveTo(size.width * .7, -10)
      ..quadraticBezierTo(size.width * .62, size.height * .38, size.width * .82, size.height + 10);

    canvas.drawPath(road3, roadBorderPaint);
    canvas.drawPath(road3, roadPaint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
