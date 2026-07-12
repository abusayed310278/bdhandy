import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'add_ticket_screen.dart';
import '../../../../core/network/api_service.dart';

class SupportTicketsScreenView extends StatefulWidget {
  const SupportTicketsScreenView({super.key});

  @override
  State<SupportTicketsScreenView> createState() => _SupportTicketsScreenViewState();
}

class _SupportTicketsScreenViewState extends State<SupportTicketsScreenView> {
  bool _isLoading = true;
  List<dynamic> _tickets = [];

  @override
  void initState() {
    super.initState();
    _fetchTickets();
  }

  Future<void> _fetchTickets() async {
    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.getTickets();
      if (response.statusCode == 200 && response.data['success'] == true) {
        setState(() {
          _tickets = response.data['data'] ?? [];
        });
      }
    } catch (e) {
      debugPrint('Error fetching tickets: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xffF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, color: Color(0xff0F172A), size: 20),
          onPressed: () => Get.back(),
        ),
        title: Text(
          'Support Tickets',
          style: GoogleFonts.poppins(
            color: const Color(0xff0F172A),
            fontSize: 16,
            fontWeight: FontWeight.w600,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.add, color: Color(0xff1293E3), size: 26),
            onPressed: () {
              Get.to(() => const AddTicketScreen());
            },
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: _isLoading 
          ? const Center(child: CircularProgressIndicator())
          : _tickets.isEmpty
          ? _buildEmptyState()
          : ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: _tickets.length,
              separatorBuilder: (context, index) => const SizedBox(height: 12),
              itemBuilder: (context, index) {
                final ticket = _tickets[index];
                return _buildTicketCard(ticket);
              },
            ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: const Color(0xFFDBEAFE),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.confirmation_number_outlined, size: 48, color: Color(0xFF3B82F6)),
          ),
          const SizedBox(height: 24),
          Text(
            'No Support Tickets',
            style: GoogleFonts.poppins(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: const Color(0xff0F172A),
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'You have not raised any support tickets yet.',
            style: GoogleFonts.poppins(
              fontSize: 14,
              color: const Color(0xff64748B),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTicketCard(dynamic ticket) {
    final status = ticket['status']?.toString().toLowerCase() ?? 'open';
    final statusColor = status == 'open' 
        ? const Color(0xFF3B82F6) 
        : (status == 'resolved' ? const Color(0xFF10B981) : const Color(0xFFF59E0B));
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
        border: Border.all(color: Colors.grey.shade100),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                ticket['ticket_number']?.toString() ?? '#TCK-0000',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: const Color(0xff64748B),
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: statusColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  status.toUpperCase(),
                  style: GoogleFonts.poppins(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                    color: statusColor,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Text(
            ticket['subject']?.toString() ?? 'No Subject',
            style: GoogleFonts.poppins(
              fontSize: 15,
              fontWeight: FontWeight.w600,
              color: const Color(0xff0F172A),
            ),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              const Icon(Icons.access_time, size: 14, color: Color(0xff94A3B8)),
              const SizedBox(width: 4),
              Text(
                'Created: ${ticket['created_at']?.toString().substring(0, 10) ?? ''}',
                style: GoogleFonts.poppins(
                  fontSize: 12,
                  color: const Color(0xff94A3B8),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
