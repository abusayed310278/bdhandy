import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'post_requirement_screen.dart';
import '../../../../core/network/api_service.dart';

class MyRequestsScreen extends StatefulWidget {
  const MyRequestsScreen({super.key});

  @override
  State<MyRequestsScreen> createState() => _MyRequestsScreenState();
}

class _MyRequestsScreenState extends State<MyRequestsScreen> {
  bool _isLoading = true;
  List<dynamic> _requirements = [];

  @override
  void initState() {
    super.initState();
    _fetchRequirements();
  }

  Future<void> _fetchRequirements() async {
    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.getRequirements();
      if (response.statusCode == 200 && response.data['success'] == true) {
        setState(() {
          _requirements = response.data['data'] ?? [];
        });
      }
    } catch (e) {
      debugPrint('Error fetching requirements: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 4,
      child: Scaffold(
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
            'My Requests',
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
                Get.to(() => const PostRequirementScreen());
              },
            ),
            const SizedBox(width: 8),
          ],
          bottom: TabBar(
            isScrollable: true,
            labelColor: const Color(0xff1293E3),
            unselectedLabelColor: const Color(0xff64748B),
            indicatorColor: const Color(0xff1293E3),
            indicatorWeight: 3,
            labelStyle: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 14),
            unselectedLabelStyle: GoogleFonts.poppins(fontWeight: FontWeight.w500, fontSize: 14),
            tabs: const [
              Tab(text: 'Open'),
              Tab(text: 'Assigned'),
              Tab(text: 'Complete'),
              Tab(text: 'Closed'),
            ],
          ),
        ),
        body: TabBarView(
          children: [
            _buildRequestList('Open'),
            _buildRequestList('Assigned'),
            _buildRequestList('Complete'),
            _buildRequestList('Closed'),
          ],
        ),
      ),
    );
  }

  Widget _buildRequestList(String status) {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    final filteredReqs = _requirements.where((r) {
      final s = (r['status'] ?? '').toString().toLowerCase();
      return s == status.toLowerCase();
    }).toList();

    if (filteredReqs.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: const Color(0xffF1F5F9),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.assignment_outlined, size: 48, color: Color(0xff94A3B8)),
            ),
            const SizedBox(height: 24),
            Text(
              'No $status Requests',
              style: GoogleFonts.poppins(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: const Color(0xff0F172A),
              ),
            ),
          ],
        ),
      );
    }

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: filteredReqs.length,
      separatorBuilder: (context, index) => const SizedBox(height: 16),
      itemBuilder: (context, index) {
        final request = filteredReqs[index];
        return _buildRequestCard(request);
      },
    );
  }

  Widget _buildRequestCard(dynamic request) {
    Color statusColor;
    Color statusBg;
    final status = (request['status'] ?? '').toString();
    final title = (request['title'] ?? 'No Title').toString();
    final date = (request['created_at'] ?? '').toString().length >= 10 
        ? request['created_at'].toString().substring(0, 10) 
        : '';

    switch (status.toLowerCase()) {
      case 'open':
        statusColor = const Color(0xFF3B82F6);
        statusBg = const Color(0xFFDBEAFE);
        break;
      case 'assigned':
        statusColor = const Color(0xFFF59E0B);
        statusBg = const Color(0xFFFEF3C7);
        break;
      case 'complete':
        statusColor = const Color(0xFF10B981);
        statusBg = const Color(0xFFD1FAE5);
        break;
      case 'closed':
        statusColor = const Color(0xFF64748B);
        statusBg = const Color(0xFFF1F5F9);
        break;
      default:
        statusColor = const Color(0xFF3B82F6);
        statusBg = const Color(0xFFDBEAFE);
    }

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
                '#REQ-${request['id'] ?? '0'}',
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: const Color(0xff64748B),
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: statusBg,
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
          const SizedBox(height: 12),
          Text(
            title,
            style: GoogleFonts.poppins(
              fontSize: 16,
              fontWeight: FontWeight.w700,
              color: const Color(0xff0F172A),
            ),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              const Icon(Icons.calendar_today_outlined, size: 14, color: Color(0xff94A3B8)),
              const SizedBox(width: 6),
              Text(
                date,
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  color: const Color(0xff64748B),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              const Icon(Icons.location_on_outlined, size: 14, color: Color(0xff94A3B8)),
              const SizedBox(width: 6),
              Expanded(
                child: Text(
                  request['address'] ?? 'Not provided',
                  style: GoogleFonts.poppins(
                    fontSize: 13,
                    color: const Color(0xff64748B),
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
