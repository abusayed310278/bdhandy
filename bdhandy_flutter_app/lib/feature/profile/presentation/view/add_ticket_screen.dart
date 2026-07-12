import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../../../core/network/api_service.dart';
import '../../../../feature/profile/presentation/view/support_tickets_screen_view.dart';

class AddTicketScreen extends StatefulWidget {
  const AddTicketScreen({super.key});

  @override
  State<AddTicketScreen> createState() => _AddTicketScreenState();
}

class _AddTicketScreenState extends State<AddTicketScreen> {
  final TextEditingController subjectController = TextEditingController();
  final TextEditingController descriptionController = TextEditingController();

  String selectedDepartment = 'Technical';
  String selectedPriority = 'Medium';

  @override
  void dispose() {
    subjectController.dispose();
    descriptionController.dispose();
    super.dispose();
  }

  InputDecoration _inputDecoration({
    required String hintText,
    Widget? prefixIcon,
  }) {
    return InputDecoration(
      hintText: hintText,
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
        borderRadius: BorderRadius.circular(14),
        borderSide: const BorderSide(color: Color(0xffE2E8F0)),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: const BorderSide(
          color: Color(0xff1D9BF0),
          width: 1.4,
        ),
      ),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
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
              fontWeight: FontWeight.w700,
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

  bool _isSubmitting = false;

  Future<void> _submitTicket() async {
    if (subjectController.text.trim().isEmpty || descriptionController.text.trim().isEmpty) {
      Get.snackbar('Error', 'Subject and description are required', backgroundColor: Colors.red, colorText: Colors.white);
      return;
    }

    setState(() => _isSubmitting = true);
    
    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.createTicket(
        subject: subjectController.text.trim(),
        details: descriptionController.text.trim(),
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        Get.snackbar(
          'Success',
          'Ticket submitted successfully',
          snackPosition: SnackPosition.TOP,
          backgroundColor: Colors.green,
          colorText: Colors.white,
        );
        Get.off(() => const SupportTicketsScreenView());
      }
    } catch (e) {
      Get.snackbar('Error', 'Failed to submit ticket', backgroundColor: Colors.red, colorText: Colors.white);
    } finally {
      setState(() => _isSubmitting = false);
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
          'Support Tickets',
          style: TextStyle(
            color: Color(0xff0F172A),
            fontWeight: FontWeight.w700,
            fontSize: 17,
          ),
        ),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(16, 6, 16, 24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Open a Support Ticket',
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.w800,
                  color: Color(0xff0F172A),
                  height: 1.15,
                ),
              ),
              const SizedBox(height: 8),
              const Text(
                'Our support team typically responds within 24 hours.',
                style: TextStyle(
                  fontSize: 14,
                  color: Color(0xff64748B),
                  height: 1.4,
                ),
              ),
              const SizedBox(height: 20),

              /// Main card
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(22),
                  border: Border.all(color: const Color(0xffE2E8F0)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.03),
                      blurRadius: 16,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    /// Top icon/title row
                    Container(
                      width: 54,
                      height: 54,
                      decoration: BoxDecoration(
                        color: const Color(0xffEAF5FF),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: const Icon(
                        Icons.support_agent,
                        color: Color(0xff1D9BF0),
                        size: 28,
                      ),
                    ),
                    const SizedBox(height: 18),

                    _label('Subject', required: true),
                    TextField(
                      controller: subjectController,
                      decoration: _inputDecoration(
                        hintText: 'Brief description of your issue',
                      ),
                    ),

                    const SizedBox(height: 16),

                    Row(
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              _label('Department', required: true),
                              DropdownButtonFormField<String>(
                                value: selectedDepartment,
                                isExpanded: true,
                                decoration: _inputDecoration(
                                  hintText: '',
                                ),
                                items: const [
                                  DropdownMenuItem(
                                    value: 'Technical',
                                    child: Text('Technical'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'Billing',
                                    child: Text('Billing'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'General',
                                    child: Text('General'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'Account',
                                    child: Text('Account'),
                                  ),
                                ],
                                onChanged: (value) {
                                  setState(() {
                                    selectedDepartment = value!;
                                  });
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
                              _label('Priority', required: true),
                              DropdownButtonFormField<String>(
                                value: selectedPriority,
                                isExpanded: true,
                                decoration: _inputDecoration(
                                  hintText: '',
                                ),
                                items: const [
                                  DropdownMenuItem(
                                    value: 'Low',
                                    child: Text('Low'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'Medium',
                                    child: Text('Medium'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'High',
                                    child: Text('High'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'Urgent',
                                    child: Text('Urgent'),
                                  ),
                                ],
                                onChanged: (value) {
                                  setState(() {
                                    selectedPriority = value!;
                                  });
                                },
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 16),

                    _label('Description', required: true),
                    TextField(
                      controller: descriptionController,
                      maxLines: 7,
                      decoration: _inputDecoration(
                        hintText:
                            'Describe your issue in detail. Include any relevant request numbers, dates, or screenshots if possible.',
                      ),
                    ),

                    const SizedBox(height: 16),

                    _label('Attachment'),
                    GestureDetector(
                      onTap: () {
                        Get.snackbar(
                          'Attachment',
                          'Add file picker here',
                          snackPosition: SnackPosition.BOTTOM,
                        );
                      },
                      child: Container(
                        width: double.infinity,
                        padding: const EdgeInsets.symmetric(
                          horizontal: 14,
                          vertical: 14,
                        ),
                        decoration: BoxDecoration(
                          color: const Color(0xffF8FAFC),
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(
                            color: const Color(0xffE2E8F0),
                            style: BorderStyle.solid,
                          ),
                        ),
                        child: Row(
                          children: [
                            Container(
                              width: 38,
                              height: 38,
                              decoration: BoxDecoration(
                                color: const Color(0xffEAF5FF),
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: const Icon(
                                Icons.attach_file,
                                color: Color(0xff1D9BF0),
                                size: 20,
                              ),
                            ),
                            const SizedBox(width: 12),
                            const Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    'Upload file or screenshot',
                                    style: TextStyle(
                                      fontSize: 14,
                                      fontWeight: FontWeight.w700,
                                      color: Color(0xff0F172A),
                                    ),
                                  ),
                                  SizedBox(height: 2),
                                  Text(
                                    'PNG, JPG, PDF up to 10MB',
                                    style: TextStyle(
                                      fontSize: 12,
                                      color: Color(0xff64748B),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            const Icon(
                              Icons.arrow_forward_ios,
                              size: 15,
                              color: Color(0xff94A3B8),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 22),

              Row(
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
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    flex: 2,
                      child: ElevatedButton(
                        onPressed: _isSubmitting ? null : _submitTicket,
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
                          'Submit Ticket',
                          style: TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
