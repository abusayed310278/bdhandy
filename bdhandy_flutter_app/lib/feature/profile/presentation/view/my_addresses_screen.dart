import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'add_address_screen.dart';

class MyAddressesScreen extends StatelessWidget {
  const MyAddressesScreen({super.key});

  @override
  Widget build(BuildContext context) {
    // Dummy address data
    final List<Map<String, dynamic>> addresses = [
      {
        'title': 'Home',
        'address': '123 Main Street, Apt 4B, New York, NY 10001',
        'isDefault': true,
        'icon': Icons.home_outlined,
      },
      {
        'title': 'Office',
        'address': '456 Business Blvd, Suite 200, New York, NY 10002',
        'isDefault': false,
        'icon': Icons.work_outline,
      },
      {
        'title': 'Parents\' House',
        'address': '789 Elm Street, Suburbia, NJ 07001',
        'isDefault': false,
        'icon': Icons.location_on_outlined,
      },
    ];

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
      body: addresses.isEmpty
          ? _buildEmptyState()
          : ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: addresses.length,
              separatorBuilder: (context, index) => const SizedBox(height: 12),
              itemBuilder: (context, index) {
                final address = addresses[index];
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

  Widget _buildAddressCard(Map<String, dynamic> address) {
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
            child: Icon(address['icon'], color: const Color(0xff64748B), size: 24),
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
                      address['title'],
                      style: GoogleFonts.poppins(
                        fontSize: 15,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xff0F172A),
                      ),
                    ),
                    if (address['isDefault'])
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
                  address['address'],
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
            onPressed: () {},
            padding: EdgeInsets.zero,
            constraints: const BoxConstraints(),
            icon: const Icon(Icons.more_vert, color: Color(0xff94A3B8), size: 20),
          )
        ],
      ),
    );
  }
}
