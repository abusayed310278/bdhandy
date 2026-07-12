import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'add_address_screen.dart';
import '../../../../core/network/api_service.dart';

class MyAddressesScreen extends StatefulWidget {
  const MyAddressesScreen({super.key});

  @override
  State<MyAddressesScreen> createState() => _MyAddressesScreenState();
}

class _MyAddressesScreenState extends State<MyAddressesScreen> {
  bool _isLoading = true;
  List<dynamic> _addresses = [];

  @override
  void initState() {
    super.initState();
    _fetchAddresses();
  }

  Future<void> _fetchAddresses() async {
    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.getAddresses();
      if (response.statusCode == 200 && response.data['success'] == true) {
        setState(() {
          _addresses = response.data['data'] ?? [];
        });
      }
    } catch (e) {
      debugPrint('Error fetching addresses: $e');
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
          'My Addresses',
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
              Get.to(() => const AddAddressScreen());
            },
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _addresses.isEmpty
          ? _buildEmptyState()
          : ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: _addresses.length,
              separatorBuilder: (context, index) => const SizedBox(height: 12),
              itemBuilder: (context, index) {
                final address = _addresses[index];
                return _buildAddressCard(address);
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
            decoration: const BoxDecoration(
              color: Color(0xFFDBEAFE),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.location_off_outlined, size: 48, color: Color(0xFF3B82F6)),
          ),
          const SizedBox(height: 24),
          Text(
            'No Addresses Found',
            style: GoogleFonts.poppins(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: const Color(0xff0F172A),
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'You have not added any addresses yet.',
            style: GoogleFonts.poppins(
              fontSize: 14,
              color: const Color(0xff64748B),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAddressCard(dynamic address) {
    final String title = address['address_type'] ?? address['label'] ?? 'Home';
    final IconData icon = title.toLowerCase() == 'home' 
        ? Icons.home_outlined 
        : (title.toLowerCase() == 'office' ? Icons.work_outline : Icons.location_on_outlined);
    
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
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: const Color(0xffF1F5F9),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: const Color(0xff64748B), size: 24),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      title,
                      style: GoogleFonts.poppins(
                        fontSize: 15,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xff0F172A),
                      ),
                    ),
                    if (address['is_primary'] == true || address['is_primary'] == 1)
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: const Color(0xFF10B981).withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          'Default',
                          style: GoogleFonts.poppins(
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: const Color(0xFF10B981),
                          ),
                        ),
                      ),
                  ],
                ),
                const SizedBox(height: 6),
                Text(
                  address['address']?.toString() ?? '',
                  style: GoogleFonts.poppins(
                    fontSize: 13,
                    color: const Color(0xff64748B),
                    height: 1.4,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 12),
          IconButton(
            onPressed: () async {
              // Delete address
              try {
                 final apiService = Get.find<ApiService>();
                 await apiService.deleteAddress(address['id']);
                 _fetchAddresses();
              } catch (e) {}
            },
            padding: EdgeInsets.zero,
            constraints: const BoxConstraints(),
            icon: const Icon(Icons.delete_outline, color: Colors.red, size: 20),
          )
        ],
      ),
    );
  }
}
