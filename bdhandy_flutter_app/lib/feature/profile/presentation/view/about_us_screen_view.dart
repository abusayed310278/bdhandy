import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../../core/colors/app_color.dart';

import '../../../../core/common/widgets/shimmer_placeholder.dart';
import '../../../../core/network/api_service.dart';

class AboutUsScreenView extends StatefulWidget {
  const AboutUsScreenView({super.key});

  @override
  State<AboutUsScreenView> createState() => _AboutUsScreenViewState();
}

class _AboutUsScreenViewState extends State<AboutUsScreenView> {
  bool _isLoading = true;
  Map<String, dynamic>? _cmsData;

  @override
  void initState() {
    super.initState();
    _fetchCmsData();
  }

  void _fetchCmsData() async {
    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.getAboutUs();
      if (response.statusCode == 200 && response.data != null) {
        if (response.data['success'] == true) {
          setState(() {
            _cmsData = response.data['data'];
            _isLoading = false;
          });
          return;
        }
      }
    } catch (_) {}
    setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        backgroundColor: AppColor.background,
        appBar: AppBar(
          backgroundColor: Colors.transparent,
          elevation: 0,
          centerTitle: true,
          title: Text(
            'About Us',
            style: GoogleFonts.poppins(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: AppColor.textPrimary,
            ),
          ),
          leading: GestureDetector(
            onTap: () => Get.back(),
            child: const Icon(Icons.arrow_back_ios_new,
                size: 18, color: AppColor.textPrimary),
          ),
        ),
        body: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 24.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const ShimmerPlaceholder.rectangular(height: 32, width: 220),
                const SizedBox(height: 12),
                const ShimmerPlaceholder.rectangular(height: 16, width: double.infinity),
                const SizedBox(height: 6),
                const ShimmerPlaceholder.rectangular(height: 16, width: 180),
                const SizedBox(height: 48),

                const ShimmerPlaceholder.rectangular(height: 24, width: 140),
                const SizedBox(height: 12),
                const ShimmerPlaceholder.rectangular(height: 14, width: double.infinity),
                const SizedBox(height: 6),
                const ShimmerPlaceholder.rectangular(height: 14, width: double.infinity),
                const SizedBox(height: 6),
                const ShimmerPlaceholder.rectangular(height: 14, width: 180),
                const SizedBox(height: 12),
                const ShimmerPlaceholder.rectangular(height: 14, width: double.infinity),
                const SizedBox(height: 6),
                const ShimmerPlaceholder.rectangular(height: 14, width: double.infinity),
                const SizedBox(height: 24),
                
                const ShimmerPlaceholder.rectangular(height: 72, width: double.infinity),
                const SizedBox(height: 48),

                const ShimmerPlaceholder.rectangular(height: 24, width: 140),
                const SizedBox(height: 12),
                const ShimmerPlaceholder.rectangular(height: 14, width: double.infinity),
                const SizedBox(height: 32),

                const ShimmerPlaceholder.rectangular(height: 24, width: 140),
                const SizedBox(height: 12),
                const ShimmerPlaceholder.rectangular(height: 14, width: double.infinity),
                const SizedBox(height: 32),

                const ShimmerPlaceholder.rectangular(height: 24, width: 140),
                const SizedBox(height: 12),
                const ShimmerPlaceholder.rectangular(height: 14, width: double.infinity),
              ],
            ),
          ),
        ),
      );
    }

    final title = _cmsData?['title'] ?? 'Redefining Beauty Discovery';
    final subtitle = _cmsData?['subtitle'] ?? 'We connect you with the best beauty & wellness specialists across the globe.';
    
    final storyTitle = _cmsData?['our_story']?['title'] ?? 'Our Story';
    final storyText = _cmsData?['our_story']?['text'] ?? 'SaloJoy started with a simple idea: booking a beauty appointment should be as easy as ordering a coffee. We noticed that many talented specialists and premium salons were hard to find or didn\'t have an online presence.\n\nToday, we are the world\'s leading appointment platform, empowering thousands of businesses to grow and helping clients look and feel their best.';
    
    final missionTitle = _cmsData?['our_mission']?['title'] ?? 'Our Mission';
    final missionText = _cmsData?['our_mission']?['text'] ?? 'To digitize the beauty industry globally, providing a seamless experience for both clients and professionals.';
    
    final visionTitle = _cmsData?['our_vision']?['title'] ?? 'Our Vision';
    final visionText = _cmsData?['our_vision']?['text'] ?? 'To become the go-to destination for wellness discovery, where every beauty need is just a few clicks away.';
    
    final valuesTitle = _cmsData?['our_values']?['title'] ?? 'Our Values';
    final valuesText = _cmsData?['our_values']?['text'] ?? 'We prioritize quality, trust, and innovation. We believe in empowering local businesses with world-class technology.';

    final statClients = _cmsData?['stats']?['clients'] ?? '10k+';
    final statClientsLabel = _cmsData?['stats']?['clients_label'] ?? 'Happy Clients';

    return Scaffold(
      backgroundColor: AppColor.background,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        title: Text(
          'About Us',
          style: GoogleFonts.poppins(
            fontSize: 18,
            fontWeight: FontWeight.w600,
            color: AppColor.textPrimary,
          ),
        ),
        leading: GestureDetector(
          onTap: () => Get.back(),
          child: const Icon(Icons.arrow_back_ios_new,
              size: 18, color: AppColor.textPrimary),
        ),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 24.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // ── Header Section ──────────────────────────────────────────
              Text(
                title,
                style: GoogleFonts.poppins(
                  fontSize: 26,
                  fontWeight: FontWeight.w700,
                  color: AppColor.textPrimary,
                  height: 1.2,
                ),
              ),
              const SizedBox(height: 12),
              Text(
                subtitle,
                style: GoogleFonts.poppins(
                  fontSize: 16,
                  color: AppColor.primary,
                  fontWeight: FontWeight.w500,
                  height: 1.5,
                ),
              ),
              const SizedBox(height: 48),

              // ── Our Story ──────────────────────────────────────────────
              _buildSectionTitle(storyTitle, Icons.auto_stories_outlined),
              const SizedBox(height: 12),
              _buildParagraph(storyText),
              const SizedBox(height: 24),
              
              // Stats
              Container(
                padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 24),
                decoration: BoxDecoration(
                  color: const Color(0xFFF3F0FF),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFE9E4FF)),
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: const Icon(Icons.favorite_rounded, color: Color(0xFFEC4899), size: 28),
                    ),
                    const SizedBox(width: 20),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          statClients,
                          style: GoogleFonts.poppins(
                            fontSize: 24,
                            fontWeight: FontWeight.w700,
                            color: const Color(0xFF6C3EE8),
                            height: 1.1,
                          ),
                        ),
                        Text(
                          statClientsLabel,
                          style: GoogleFonts.poppins(
                            fontSize: 14,
                            fontWeight: FontWeight.w500,
                            color: AppColor.textSecondary,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              
              const SizedBox(height: 48),

              // ── Our Mission ────────────────────────────────────────────
              _buildSectionTitle(missionTitle, Icons.track_changes_outlined),
              const SizedBox(height: 12),
              _buildParagraph(missionText),
              const SizedBox(height: 32),

              // ── Our Vision ─────────────────────────────────────────────
              _buildSectionTitle(visionTitle, Icons.visibility_outlined),
              const SizedBox(height: 12),
              _buildParagraph(visionText),
              const SizedBox(height: 32),

              // ── Our Values ─────────────────────────────────────────────
              _buildSectionTitle(valuesTitle, Icons.diamond_outlined),
              const SizedBox(height: 12),
              _buildParagraph(valuesText),

              const SizedBox(height: 48),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title, IconData icon) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Icon(icon, color: AppColor.primary, size: 22),
        const SizedBox(width: 8),
        Text(
          title,
          style: GoogleFonts.poppins(
            fontSize: 20,
            fontWeight: FontWeight.w700,
            color: AppColor.textPrimary,
          ),
        ),
      ],
    );
  }

  Widget _buildParagraph(String text) {
    return Text(
      text,
      style: GoogleFonts.poppins(
        fontSize: 14,
        color: AppColor.textSecondary,
        height: 1.6,
      ),
    );
  }
}
