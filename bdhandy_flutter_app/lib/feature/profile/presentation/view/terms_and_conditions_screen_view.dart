import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../../core/colors/app_color.dart';
import '../../../../core/common/widgets/shimmer_placeholder.dart';
import '../../../../core/network/api_service.dart';

class TermsAndConditionsScreenView extends StatefulWidget {
  const TermsAndConditionsScreenView({super.key});

  @override
  State<TermsAndConditionsScreenView> createState() => _TermsAndConditionsScreenViewState();
}

class _TermsAndConditionsScreenViewState extends State<TermsAndConditionsScreenView> {
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
      final response = await apiService.getTermsConditions();
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
            'Terms & Conditions',
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
        body: const SafeArea(
          child: SingleChildScrollView(
            padding: EdgeInsets.symmetric(horizontal: 24.0, vertical: 24.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                ShimmerPlaceholder.rectangular(height: 20, width: 180),
                SizedBox(height: 16),
                ShimmerPlaceholder.rectangular(height: 80, width: double.infinity),
                SizedBox(height: 24),
                ShimmerPlaceholder.rectangular(height: 24, width: 220),
                SizedBox(height: 12),
                ShimmerPlaceholder.rectangular(height: 16, width: double.infinity),
                SizedBox(height: 8),
                ShimmerPlaceholder.rectangular(height: 16, width: double.infinity),
                SizedBox(height: 8),
                ShimmerPlaceholder.rectangular(height: 16, width: 180),
                SizedBox(height: 24),
                ShimmerPlaceholder.rectangular(height: 24, width: 200),
                SizedBox(height: 12),
                ShimmerPlaceholder.rectangular(height: 16, width: double.infinity),
                SizedBox(height: 8),
                ShimmerPlaceholder.rectangular(height: 16, width: double.infinity),
              ],
            ),
          ),
        ),
      );
    }

    final title = _cmsData?['title'] ?? 'Terms & Conditions';
    final lastUpdated = _cmsData?['last_updated'] ?? 'Last Updated: June 29, 2026';
    final intro = _cmsData?['intro'] ?? 'These Terms & Conditions ("Terms") govern your access to and use of the Salojoy platform, including our website at salojoy.com and our mobile applications (collectively, the "Platform"). By registering an account or using the Platform in any way, you agree to be bound by these Terms. If you do not agree, please do not use the Platform.';
    final sections = _cmsData?['sections'] as List?;

    return Scaffold(
      backgroundColor: AppColor.background,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        title: Text(
          title,
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
          padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildSectionTitle(lastUpdated),
              
              if (sections != null) ...[
                if (intro.isNotEmpty) _buildParagraph(intro),
                for (var section in sections) ...[
                  if (section['title'] != null && section['title'].toString().isNotEmpty)
                    _buildSectionTitle(section['title']),
                  if (section['description'] != null && section['description'].toString().isNotEmpty)
                    _buildParagraph(section['description']),
                  if (section['items'] != null && section['items'] is List) ...[
                    for (var item in section['items'])
                      _buildBulletPoint(item.toString()),
                  ],
                  if (section['subsections'] != null && section['subsections'] is List) ...[
                    for (var sub in section['subsections']) ...[
                      if (sub['title'] != null && sub['title'].toString().isNotEmpty)
                        _buildSubSectionTitle(sub['title']),
                      if (sub['items'] != null && sub['items'] is List) ...[
                        for (var subItem in sub['items'])
                          _buildBulletPoint(subItem.toString()),
                      ],
                    ],
                  ],
                  if (section['footer'] != null && section['footer'].toString().isNotEmpty)
                    _buildParagraph(section['footer']),
                ]
              ] else ...[
                // Fallback to static contents
                _buildParagraph(intro),
                _buildSectionTitle('1. Definitions'),
                _buildBulletPoint('"Salojoy", "we", "us", "our" — refers to Salojoy and its operators.'),
                _buildBulletPoint('"Platform" — the Salojoy website, web application, and mobile applications.'),
                _buildBulletPoint('"User" — any individual who accesses or uses the Platform, including clients and business owners.'),
                _buildBulletPoint('"Client" — a User who uses the Platform to discover and book appointments at salons or beauty businesses.'),
                _buildBulletPoint('"Business Owner / Merchant" — a User who registers a salon, spa, barbershop, or other beauty business on the Platform to manage bookings, staff, and operations.'),
                _buildBulletPoint('"Services" — all features, tools, and functionality provided by Salojoy, including booking management, POS, staff management, inventory, and analytics.'),
                
                _buildSectionTitle('2. Eligibility'),
                _buildParagraph('You must be at least 18 years of age to create an account and use the Platform. By using Salojoy, you represent and warrant that you are 18 or older, have the legal capacity to enter into these Terms, and will comply with all applicable laws in Canada and your jurisdiction.'),
                
                _buildSectionTitle('3. Account Registration & Security'),
                _buildParagraph('To access certain features, you must register for an account. You agree to:'),
                _buildBulletPoint('Provide accurate, current, and complete information during registration.'),
                _buildBulletPoint('Maintain and promptly update your account information.'),
                _buildBulletPoint('Keep your password confidential and not share it with third parties.'),
                _buildBulletPoint('Notify us immediately at info@salojoy.com of any unauthorized use of your account.'),
                _buildBulletPoint('Accept responsibility for all activities that occur under your account.'),
                _buildParagraph('We reserve the right to suspend or terminate accounts that contain false information or that are used in violation of these Terms.'),

                _buildSectionTitle('4. Acceptable Use'),
                _buildParagraph('You agree to use the Platform only for lawful purposes. You must NOT:'),
                _buildBulletPoint('Post false, misleading, or fraudulent business listings or reviews.'),
                _buildBulletPoint('Use the Platform to harass, abuse, or harm any person.'),
                _buildBulletPoint('Attempt to gain unauthorized access to any part of the Platform or its related systems.'),
                _buildBulletPoint('Use automated bots, scrapers, or other tools to extract data from the Platform without written permission.'),
                _buildBulletPoint('Interfere with the normal operation or security of the Platform.'),
                _buildBulletPoint('Use the Platform for any purpose that violates the laws of Canada or your local jurisdiction.'),
                _buildBulletPoint('Impersonate another person, business, or organization.'),
                _buildBulletPoint('Upload or transmit malicious code, viruses, or harmful content of any kind.'),
                _buildParagraph('Violation of this section may result in immediate account termination and potential legal action.'),

                _buildSectionTitle('5. Booking & Salon Services'),
                
                _buildSubSectionTitle('For Clients'),
                _buildBulletPoint('When you book an appointment through Salojoy, you enter into a direct agreement with the salon or business. Salojoy acts as a booking facilitator, not a party to the service agreement.'),
                _buildBulletPoint('You are responsible for arriving on time for your appointment. Late arrivals may result in shortened or cancelled services at the discretion of the salon.'),
                _buildBulletPoint('Cancellations must be made within the cancellation window specified by the individual salon. Failure to cancel in time may result in a cancellation fee.'),
                _buildBulletPoint('No-shows without notice may result in restrictions on future bookings on the Platform.'),

                _buildSubSectionTitle('For Business Owners'),
                _buildBulletPoint('You are responsible for fulfilling all appointments booked through your Salojoy profile.'),
                _buildBulletPoint('You must keep your availability, services, and pricing accurately updated on the Platform.'),
                _buildBulletPoint('You must have a clearly stated cancellation policy visible to clients before they book.'),
                _buildBulletPoint('Salojoy is not liable for disputes between clients and your business arising from services rendered.'),

                _buildSectionTitle('6. Payments & Fees'),
                _buildParagraph('Salojoy offers subscription plans for Business Owners to access platform features. All fees are stated in US Dollars (USD) unless otherwise indicated and are subject to applicable taxes.'),
                _buildBulletPoint('Subscription fees are billed on a monthly or annual basis and are non-refundable except as required by law.'),
                _buildBulletPoint('Payments are processed by trusted third-party processors (Payme, Click, or Stripe). Salojoy does not store full card details.'),
                _buildBulletPoint('In-app client payments for services are processed at the point of booking or point of sale; the salon is responsible for any refunds related to services rendered.'),
                _buildBulletPoint('Salojoy reserves the right to change pricing with at least 30 days\' notice. Continued use after the new price takes effect constitutes acceptance.'),

                _buildSectionTitle('7. Intellectual Property'),
                _buildParagraph('All content on the Platform — including the Salojoy name, logo, design, software, text, graphics, and features — is the exclusive property of Salojoy and is protected by applicable intellectual property laws. You may not copy, reproduce, distribute, or create derivative works from any part of the Platform without our express written permission.'),
                _buildParagraph('By uploading content (photos, business descriptions, service listings) to the Platform, you grant Salojoy a non-exclusive, royalty-free, worldwide license to use, display, and distribute that content solely for the purpose of operating and promoting the Platform.'),

                _buildSectionTitle('8. Reviews & User Content'),
                _buildParagraph('Clients may leave reviews for salons they have visited. By submitting a review, you confirm that it is based on your genuine experience. You must not post fake, defamatory, or incentivized reviews. Salojoy reserves the right to remove reviews that violate these Terms or that are reported as abusive. We do not endorse or guarantee the accuracy of any user-generated content.'),

                _buildSectionTitle('9. Limitation of Liability'),
                _buildParagraph('To the maximum extent permitted by applicable law, Salojoy and its officers, directors, employees, and affiliates shall not be liable for:'),
                _buildBulletPoint('Any indirect, incidental, special, consequential, or punitive damages.'),
                _buildBulletPoint('Loss of revenue, data, goodwill, or business opportunities arising from use of the Platform.'),
                _buildBulletPoint('The quality, safety, legality, or any aspect of the beauty services provided by businesses listed on Salojoy.'),
                _buildBulletPoint('Any technical issues, downtime, or interruptions in Platform availability.'),
                _buildBulletPoint('Actions or omissions of third-party service providers (payment processors, SMS providers, etc.).'),
                _buildParagraph('Our total liability to you for any claim arising from use of the Platform shall not exceed the amount you paid to Salojoy in the 3 months preceding the claim.'),

                _buildSectionTitle('10. Termination'),
                _buildParagraph('You may close your account at any time by contacting us or using the account deletion option in your settings. We reserve the right to suspend or permanently terminate your account without notice if you violate these Terms, engage in fraudulent activity, or pose a risk to other users or the Platform. Upon termination, your right to use the Platform ceases immediately.'),

                _buildSectionTitle('11. Changes to These Terms'),
                _buildParagraph('We may revise these Terms at any time. When we make material changes, we will notify you via email or a prominent notice on the Platform at least 14 days before the changes take effect. Continued use of the Platform after the effective date constitutes your acceptance of the revised Terms.'),

                _buildSectionTitle('12. Governing Law & Disputes'),
                _buildParagraph('These Terms are governed by the laws of Canada. Any disputes arising from these Terms or your use of the Platform shall first be attempted to be resolved amicably. If not resolved within 30 days, disputes shall be submitted to the competent courts of Vancouver, Canada.'),

                const SizedBox(height: 16),
                const Divider(color: Color(0xFFE5E7EB)),
                const SizedBox(height: 16),
                
                _buildSectionTitle('Contact Us'),
                _buildParagraph('If you have questions about these Terms, please contact us:'),
                _buildParagraph('SaloJoy\nVancouver, Canada\nEmail: info@salojoy.com\nPhone: +1 778 804 9199'),
              ],
              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(top: 24, bottom: 8),
      child: Text(
        title,
        style: GoogleFonts.poppins(
          fontSize: 16,
          fontWeight: FontWeight.w700,
          color: AppColor.textPrimary,
        ),
      ),
    );
  }

  Widget _buildSubSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(top: 16, bottom: 8),
      child: Text(
        title,
        style: GoogleFonts.poppins(
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: AppColor.textPrimary,
        ),
      ),
    );
  }

  Widget _buildParagraph(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Text(
        text,
        style: GoogleFonts.poppins(
          fontSize: 13,
          color: AppColor.textSecondary,
          height: 1.6,
        ),
      ),
    );
  }

  Widget _buildBulletPoint(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8, left: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            '• ',
            style: GoogleFonts.poppins(
              fontSize: 16,
              color: AppColor.primary,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(width: 4),
          Expanded(
            child: Text(
              text,
              style: GoogleFonts.poppins(
                fontSize: 13,
                color: AppColor.textSecondary,
                height: 1.6,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
