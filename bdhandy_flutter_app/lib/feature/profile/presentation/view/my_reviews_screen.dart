import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';

class MyReviewsScreen extends StatelessWidget {
  const MyReviewsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    // Dummy review data
    final List<Map<String, dynamic>> reviews = [
      {
        'providerName': 'Rahim Electric',
        'service': 'AC Repair',
        'rating': 5,
        'date': 'Oct 15, 2026',
        'comment': 'Excellent service! The technician arrived on time and fixed the issue quickly.',
        'image': 'assets/images/handyman.png',
      },
      {
        'providerName': 'Cleaning Experts',
        'service': 'Deep House Cleaning',
        'rating': 4,
        'date': 'Sep 28, 2026',
        'comment': 'Good work overall, but they missed a few spots under the sofa. Still satisfied with the service.',
        'image': 'assets/images/handyman.png',
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
          'My Reviews',
          style: GoogleFonts.poppins(
            color: const Color(0xff0F172A),
            fontSize: 16,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),
      body: reviews.isEmpty
          ? _buildEmptyState()
          : ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: reviews.length,
              separatorBuilder: (context, index) => const SizedBox(height: 16),
              itemBuilder: (context, index) {
                final review = reviews[index];
                return _buildReviewCard(review);
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
              color: Color(0xFFFEF3C7),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.star_border, size: 48, color: Color(0xFFF59E0B)),
          ),
          const SizedBox(height: 24),
          Text(
            'No Reviews Yet',
            style: GoogleFonts.poppins(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: const Color(0xff0F172A),
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'You have not reviewed any providers yet.',
            style: GoogleFonts.poppins(
              fontSize: 14,
              color: const Color(0xff64748B),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildReviewCard(Map<String, dynamic> review) {
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
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: const BoxDecoration(
                  color: Color(0xffF1F5F9),
                  shape: BoxShape.circle,
                ),
                child: ClipOval(
                  child: Image.asset(
                    review['image'],
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) => const Icon(Icons.person, color: Color(0xff94A3B8)),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      review['providerName'],
                      style: GoogleFonts.poppins(
                        fontSize: 15,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xff0F172A),
                      ),
                    ),
                    Text(
                      review['service'],
                      style: GoogleFonts.poppins(
                        fontSize: 12,
                        color: const Color(0xff64748B),
                      ),
                    ),
                  ],
                ),
              ),
              Text(
                review['date'],
                style: GoogleFonts.poppins(
                  fontSize: 12,
                  color: const Color(0xff94A3B8),
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          Row(
            children: List.generate(5, (index) {
              return Icon(
                index < review['rating'] ? Icons.star : Icons.star_border,
                color: const Color(0xFFF59E0B),
                size: 16,
              );
            }),
          ),
          const SizedBox(height: 10),
          Text(
            review['comment'],
            style: GoogleFonts.poppins(
              fontSize: 13,
              color: const Color(0xff334155),
              height: 1.5,
            ),
          ),
        ],
      ),
    );
  }
}
