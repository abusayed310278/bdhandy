import 'package:flutter/material.dart';
import 'package:get/get.dart';

class PostRequirementScreen extends StatefulWidget {
  const PostRequirementScreen({super.key});

  @override
  State<PostRequirementScreen> createState() => _PostRequirementScreenState();
}

class _PostRequirementScreenState extends State<PostRequirementScreen> {
  final _formKey = GlobalKey<FormState>();

  final TextEditingController titleController = TextEditingController();
  final TextEditingController descriptionController = TextEditingController();
  final TextEditingController preferredDateController = TextEditingController();
  final TextEditingController fixedBudgetController = TextEditingController();
  final TextEditingController locationController = TextEditingController();

  String? selectedCategory;
  String? selectedService;
  String urgencyLevel = 'Normal';
  String budgetType = 'Fixed Price';
  String selectedCurrency = 'BDT';
  String locationType = 'Home';

  @override
  void dispose() {
    titleController.dispose();
    descriptionController.dispose();
    preferredDateController.dispose();
    fixedBudgetController.dispose();
    locationController.dispose();
    super.dispose();
  }

  void _submitRequirement() {
    if (!_formKey.currentState!.validate()) return;

    Get.snackbar(
      'Success',
      'Requirement posted successfully',
      snackPosition: SnackPosition.BOTTOM,
      backgroundColor: Colors.white,
      colorText: Colors.black87,
      margin: const EdgeInsets.all(14),
    );
  }

  InputDecoration _inputDecoration({
    required String hint,
    Widget? prefixIcon,
    Widget? suffixIcon,
  }) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(
        color: Color(0xff94A3B8),
        fontSize: 14,
        fontWeight: FontWeight.w500,
      ),
      prefixIcon: prefixIcon,
      suffixIcon: suffixIcon,
      filled: true,
      fillColor: const Color(0xffF8FAFC),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 15),
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
              fontSize: 12,
              fontWeight: FontWeight.w800,
              color: Color(0xff0F172A),
              letterSpacing: 0.2,
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
    required Color iconBg,
    required Color iconColor,
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
                width: 38,
                height: 38,
                decoration: BoxDecoration(
                  color: iconBg,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, size: 20, color: iconColor),
              ),
              const SizedBox(width: 10),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w900,
                  color: Color(0xff0F172A),
                ),
              ),
            ],
          ),
          const SizedBox(height: 18),
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
        color: Color(0xff475569),
      ),
      decoration: _inputDecoration(hint: hint),
      items: items
          .map(
            (item) => DropdownMenuItem<String>(
              value: item,
              child: Text(
                item,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: Color(0xff0F172A),
                ),
              ),
            ),
          )
          .toList(),
      onChanged: onChanged,
      validator: validator,
    );
  }

  Widget _urgencyCard({
    required String title,
    required String subtitle,
    required bool selected,
    required VoidCallback onTap,
    required Color dotColor,
  }) {
    return Expanded(
      child: GestureDetector(
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: selected ? const Color(0xffEFF6FF) : Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: selected ? const Color(0xff1D9BF0) : const Color(0xffE2E8F0),
              width: selected ? 1.5 : 1,
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    width: 9,
                    height: 9,
                    decoration: BoxDecoration(
                      color: dotColor,
                      shape: BoxShape.circle,
                    ),
                  ),
                  const Spacer(),
                  Icon(
                    selected ? Icons.check_circle : Icons.radio_button_off,
                    size: 18,
                    color: selected ? const Color(0xff1D9BF0) : const Color(0xff94A3B8),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w800,
                  color: Color(0xff0F172A),
                ),
              ),
              const SizedBox(height: 4),
              Text(
                subtitle,
                style: const TextStyle(
                  fontSize: 11,
                  height: 1.35,
                  color: Color(0xff64748B),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _budgetTypeTabs() {
    final tabs = ['Fixed Price', 'Price Range', 'Negotiable'];

    return Row(
      children: tabs.map((tab) {
        final isSelected = budgetType == tab;
        return Expanded(
          child: GestureDetector(
            onTap: () {
              setState(() {
                budgetType = tab;
              });
            },
            child: Container(
              margin: EdgeInsets.only(right: tab != tabs.last ? 8 : 0),
              padding: const EdgeInsets.symmetric(vertical: 11),
              decoration: BoxDecoration(
                color: isSelected ? const Color(0xffEAF5FF) : const Color(0xffF8FAFC),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(
                  color: isSelected ? const Color(0xff1D9BF0) : const Color(0xffE2E8F0),
                ),
              ),
              child: Center(
                child: Text(
                  tab,
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w800,
                    color: isSelected ? const Color(0xff1D9BF0) : const Color(0xff475569),
                  ),
                ),
              ),
            ),
          ),
        );
      }).toList(),
    );
  }

  Widget _locationTypeCard({
    required String title,
    required String subtitle,
    required String value,
    required IconData icon,
  }) {
    final bool selected = locationType == value;

    return Expanded(
      child: GestureDetector(
        onTap: () {
          setState(() {
            locationType = value;
          });
        },
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: selected ? const Color(0xffEFF6FF) : Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: selected ? const Color(0xff1D9BF0) : const Color(0xffE2E8F0),
              width: selected ? 1.5 : 1,
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(icon, size: 18, color: selected ? const Color(0xff1D9BF0) : const Color(0xff64748B)),
                  const Spacer(),
                  Icon(
                    selected ? Icons.check_circle : Icons.radio_button_off,
                    size: 18,
                    color: selected ? const Color(0xff1D9BF0) : const Color(0xff94A3B8),
                  ),
                ],
              ),
              const SizedBox(height: 10),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w800,
                  color: Color(0xff0F172A),
                ),
              ),
              const SizedBox(height: 4),
              Text(
                subtitle,
                style: const TextStyle(
                  fontSize: 11,
                  height: 1.35,
                  color: Color(0xff64748B),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _mapPreview() {
    return Container(
      height: 170,
      width: double.infinity,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xffE2E8F0)),
        color: const Color(0xffF1F5F9),
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(18),
        child: Stack(
          children: [
            Positioned.fill(
              child: CustomPaint(
                painter: _MapBackgroundPainter(),
              ),
            ),
            const Center(
              child: Icon(
                Icons.location_on,
                color: Color(0xffEF4444),
                size: 42,
              ),
            ),
            Positioned(
              right: 12,
              bottom: 12,
              child: Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.07),
                      blurRadius: 12,
                    ),
                  ],
                ),
                child: const Icon(
                  Icons.my_location,
                  size: 20,
                  color: Color(0xff1D9BF0),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _uploadBox() {
    return GestureDetector(
      onTap: () {
        Get.snackbar(
          'Upload',
          'Add file picker here',
          snackPosition: SnackPosition.BOTTOM,
        );
      },
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 26),
        decoration: BoxDecoration(
          color: const Color(0xffF8FAFC),
          borderRadius: BorderRadius.circular(18),
          border: Border.all(
            color: const Color(0xffD7E7F5),
            style: BorderStyle.solid,
          ),
        ),
        child: Column(
          children: [
            Container(
              width: 52,
              height: 52,
              decoration: BoxDecoration(
                color: const Color(0xffEAF5FF),
                borderRadius: BorderRadius.circular(16),
              ),
              child: const Icon(
                Icons.cloud_upload_outlined,
                color: Color(0xff1D9BF0),
                size: 28,
              ),
            ),
            const SizedBox(height: 12),
            const Text(
              'Click to upload or drag and drop',
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w800,
                color: Color(0xff0F172A),
              ),
            ),
            const SizedBox(height: 5),
            const Text(
              'JPG, PNG, PDF — up to 5 files',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 12,
                color: Color(0xff64748B),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _pickPreferredDate() async {
    final date = await showDatePicker(
      context: context,
      initialDate: DateTime.now().add(const Duration(days: 1)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );

    if (date != null) {
      preferredDateController.text =
          '${date.day.toString().padLeft(2, '0')}/${date.month.toString().padLeft(2, '0')}/${date.year}';
    }
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
          'My Requirements',
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
                child: ElevatedButton.icon(
                  onPressed: _submitRequirement,
                  icon: const Icon(Icons.send_rounded, size: 18),
                  label: const Text(
                    'Post Requirement',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  style: ElevatedButton.styleFrom(
                    minimumSize: const Size.fromHeight(54),
                    backgroundColor: const Color(0xff1D9BF0),
                    foregroundColor: Colors.white,
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
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
                  'Post a Requirement',
                  style: TextStyle(
                    fontSize: 28,
                    fontWeight: FontWeight.w900,
                    color: Color(0xff0F172A),
                    height: 1.15,
                  ),
                ),
                const SizedBox(height: 8),
                const Text(
                  'Describe your service need and receive quality proposals from nearby providers.',
                  style: TextStyle(
                    fontSize: 14,
                    color: Color(0xff64748B),
                    height: 1.45,
                  ),
                ),
                const SizedBox(height: 20),

                _sectionCard(
                  title: 'Basic Information',
                  icon: Icons.description_outlined,
                  iconBg: const Color(0xffEAF5FF),
                  iconColor: const Color(0xff1D9BF0),
                  children: [
                    _label('Title / Headline', required: true),
                    TextFormField(
                      controller: titleController,
                      decoration: _inputDecoration(
                        hint: 'e.g. Need a professional electrician to install ceiling fans',
                      ),
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Please enter title';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    _label('Detailed Description', required: true),
                    TextFormField(
                      controller: descriptionController,
                      maxLines: 5,
                      decoration: _inputDecoration(
                        hint: 'Describe your job in detail. Include expected scope, brand, or special challenges.',
                      ),
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Please enter description';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              _label('Category', required: true),
                              _dropdownField(
                                value: selectedCategory,
                                hint: 'Select category',
                                items: const [
                                  'Electrical',
                                  'Plumbing',
                                  'Painting',
                                  'Cleaning',
                                  'Interior Design',
                                  'Appliance Repair',
                                ],
                                onChanged: (value) {
                                  setState(() {
                                    selectedCategory = value;
                                  });
                                },
                                validator: (value) {
                                  if (value == null) return 'Select category';
                                  return null;
                                },
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              _label('Specific Service', required: true),
                              _dropdownField(
                                value: selectedService,
                                hint: 'Select service',
                                items: const [
                                  'Fan Installation',
                                  'Light Installation',
                                  'Wiring Repair',
                                  'Deep Cleaning',
                                  'Wall Painting',
                                  'Leak Fixing',
                                ],
                                onChanged: (value) {
                                  setState(() {
                                    selectedService = value;
                                  });
                                },
                                validator: (value) {
                                  if (value == null) return 'Select service';
                                  return null;
                                },
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),

                const SizedBox(height: 18),

                _sectionCard(
                  title: 'Timing & Urgency',
                  icon: Icons.schedule_outlined,
                  iconBg: const Color(0xffFFF7ED),
                  iconColor: const Color(0xffF97316),
                  children: [
                    _label('Urgency Level', required: true),
                    Row(
                      children: [
                        _urgencyCard(
                          title: 'Normal',
                          subtitle: 'Standard timing priority',
                          selected: urgencyLevel == 'Normal',
                          dotColor: const Color(0xff475569),
                          onTap: () => setState(() => urgencyLevel = 'Normal'),
                        ),
                        const SizedBox(width: 10),
                        _urgencyCard(
                          title: 'Urgent',
                          subtitle: 'Faster matching response',
                          selected: urgencyLevel == 'Urgent',
                          dotColor: const Color(0xffF59E0B),
                          onTap: () => setState(() => urgencyLevel = 'Urgent'),
                        ),
                        const SizedBox(width: 10),
                        _urgencyCard(
                          title: 'Emergency',
                          subtitle: 'Immediate service need',
                          selected: urgencyLevel == 'Emergency',
                          dotColor: const Color(0xffEF4444),
                          onTap: () => setState(() => urgencyLevel = 'Emergency'),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    _label('Preferred Service Date (Optional)'),
                    TextFormField(
                      controller: preferredDateController,
                      readOnly: true,
                      onTap: _pickPreferredDate,
                      decoration: _inputDecoration(
                        hint: 'dd/mm/yyyy',
                        suffixIcon: const Icon(
                          Icons.calendar_today_outlined,
                          size: 18,
                          color: Color(0xff64748B),
                        ),
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 18),

                _sectionCard(
                  title: 'Budget Details',
                  icon: Icons.account_balance_wallet_outlined,
                  iconBg: const Color(0xffECFDF5),
                  iconColor: const Color(0xff16A34A),
                  children: [
                    _budgetTypeTabs(),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              _label(
                                budgetType == 'Price Range'
                                    ? 'Price From'
                                    : budgetType == 'Negotiable'
                                        ? 'Budget Hint'
                                        : 'Fixed Price Amount',
                              ),
                              TextFormField(
                                controller: fixedBudgetController,
                                keyboardType: TextInputType.number,
                                decoration: _inputDecoration(
                                  hint: '0.00',
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              _label('Currency'),
                              _dropdownField(
                                value: selectedCurrency,
                                hint: 'Select currency',
                                items: const ['BDT', 'USD', 'AED', 'PKR'],
                                onChanged: (value) {
                                  setState(() {
                                    selectedCurrency = value!;
                                  });
                                },
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),

                const SizedBox(height: 18),

                _sectionCard(
                  title: 'Service Location',
                  icon: Icons.location_on_outlined,
                  iconBg: const Color(0xffEEF2FF),
                  iconColor: const Color(0xff4F46E5),
                  children: [
                    _label('Select Location Type'),
                    Row(
                      children: [
                        _locationTypeCard(
                          title: 'Home',
                          subtitle: 'House, flat, building, apartment',
                          value: 'Home',
                          icon: Icons.home_outlined,
                        ),
                        const SizedBox(width: 12),
                        _locationTypeCard(
                          title: 'Others',
                          subtitle: 'Office, shop, commercial or custom address',
                          value: 'Others',
                          icon: Icons.add_location_alt_outlined,
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    _label('Location / Address'),
                    TextFormField(
                      controller: locationController,
                      decoration: _inputDecoration(
                        hint: 'Lake Rd, Dhaka, Bangladesh',
                        prefixIcon: const Icon(
                          Icons.search,
                          color: Color(0xff94A3B8),
                        ),
                      ),
                    ),
                    const SizedBox(height: 14),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                      decoration: BoxDecoration(
                        color: const Color(0xffF8FAFC),
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: const Color(0xffE2E8F0)),
                      ),
                      child: const Row(
                        children: [
                          Icon(Icons.my_location, size: 16, color: Color(0xff1D9BF0)),
                          SizedBox(width: 8),
                          Text(
                            'Lat: 23.7696    Long: 90.3928',
                            style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.w700,
                              color: Color(0xff475569),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 14),
                    _mapPreview(),
                    const SizedBox(height: 14),
                    Row(
                      children: const [
                        Text(
                          'Target Search Radius',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: Color(0xff0F172A),
                          ),
                        ),
                        Spacer(),
                        Text(
                          '10 KM',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w800,
                            color: Color(0xff1D9BF0),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    SliderTheme(
                      data: SliderTheme.of(context).copyWith(
                        activeTrackColor: const Color(0xff1D9BF0),
                        inactiveTrackColor: const Color(0xffDCEBFA),
                        thumbColor: const Color(0xff1D9BF0),
                        overlayColor: const Color(0xff1D9BF0).withOpacity(0.16),
                        trackHeight: 5,
                      ),
                      child: Slider(
                        value: 10,
                        min: 1,
                        max: 50,
                        onChanged: (_) {},
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 18),

                _sectionCard(
                  title: 'Reference Photos / Documents',
                  icon: Icons.attach_file_outlined,
                  iconBg: const Color(0xffF5F3FF),
                  iconColor: const Color(0xff7C3AED),
                  children: [
                    const Text(
                      'Upload any related photos, designs, or PDF details to help providers understand your request.',
                      style: TextStyle(
                        fontSize: 13,
                        color: Color(0xff64748B),
                        height: 1.45,
                      ),
                    ),
                    const SizedBox(height: 14),
                    _uploadBox(),
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

class _MapBackgroundPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final bg = Paint()..color = const Color(0xffEAF5FF);
    canvas.drawRect(Offset.zero & size, bg);

    final green = Paint()
      ..color = const Color(0xffD1FAE5)
      ..style = PaintingStyle.fill;

    canvas.drawCircle(Offset(size.width * .2, size.height * .25), 45, green);
    canvas.drawCircle(Offset(size.width * .72, size.height * .65), 55, green);

    final roadBorder = Paint()
      ..color = const Color(0xffCFE3F7)
      ..strokeWidth = 12
      ..strokeCap = StrokeCap.round;

    final road = Paint()
      ..color = Colors.white
      ..strokeWidth = 8
      ..strokeCap = StrokeCap.round;

    final path1 = Path()
      ..moveTo(0, size.height * .28)
      ..quadraticBezierTo(size.width * .25, size.height * .35, size.width * .5, size.height * .22)
      ..quadraticBezierTo(size.width * .72, size.height * .12, size.width, size.height * .25);

    final path2 = Path()
      ..moveTo(size.width * .18, 0)
      ..quadraticBezierTo(size.width * .36, size.height * .35, size.width * .28, size.height);

    final path3 = Path()
      ..moveTo(size.width * .78, 0)
      ..quadraticBezierTo(size.width * .64, size.height * .4, size.width * .84, size.height);

    canvas.drawPath(path1, roadBorder);
    canvas.drawPath(path1, road);

    canvas.drawPath(path2, roadBorder);
    canvas.drawPath(path2, road);

    canvas.drawPath(path3, roadBorder);
    canvas.drawPath(path3, road);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
