import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../../core/colors/app_color.dart';
import '../../../../core/common/widgets/shimmer_placeholder.dart';
import '../../../../core/network/api_service.dart';

class PrivacyPolicyScreenView extends StatefulWidget {
  const PrivacyPolicyScreenView({super.key});

  @override
  State<PrivacyPolicyScreenView> createState() => _PrivacyPolicyScreenViewState();
}

class _PrivacyPolicyScreenViewState extends State<PrivacyPolicyScreenView> {
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
      final response = await apiService.getPrivacyPolicy();
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
            'Privacy Policy',
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

    final title = _cmsData?['title'] ?? 'Privacy Policy';
    final lastUpdated = _cmsData?['last_updated'] ?? 'Last Updated: June 29, 2026';
    final intro = _cmsData?['intro'] ?? 'Welcome to Salojoy. We operate the website salojoy.com and our mobile applications (collectively, the "Platform"). This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our Platform. Please read it carefully. By using Salojoy, you agree to the practices described in this policy.';
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
                _buildSectionTitle('We collect information in the following ways:'),
                
                _buildSubSectionTitle('a) Information You Provide Directly'),
                _buildBulletPoint('Account Registration: When you create an account, we collect your full name, email address, phone number, and password.'),
                _buildBulletPoint('Business Profiles: Salon owners and managers provide business name, address, working hours, service listings, staff details, and payment information.'),
                _buildBulletPoint('Bookings: When you book an appointment, we collect the date, time, selected service, and preferred staff member.'),
                _buildBulletPoint('Contact Forms: Any messages or inquiries you send us through our contact page.'),
                _buildBulletPoint('Profile Information: Optional details such as a profile photo or personal preferences.'),
                
                _buildSubSectionTitle('b) Information Collected Automatically'),
                _buildBulletPoint('Usage Data: Pages visited, features used, time spent on the Platform, and interactions with listings.'),
                _buildBulletPoint('Device Information: IP address, browser type, operating system, and device identifiers.'),
                _buildBulletPoint('Location Data: Approximate location based on IP address; precise location only if you grant permission in the mobile app.'),
                _buildBulletPoint('Cookies & Tracking: We use cookies and similar technologies to maintain sessions, remember preferences, and analyze usage patterns.'),

                _buildSubSectionTitle('c) Information from Third Parties'),
                _buildBulletPoint('Payment Processors: When you make a payment, our payment partners (e.g., Payme, Click, Stripe) process your payment details. We only receive a transaction confirmation and masked payment identifier — we never store full card numbers.'),
                _buildBulletPoint('Social Login: If you sign in via Google or other providers, we receive your name, email, and profile picture from that provider.'),
                
                _buildSectionTitle('2. How We Use Your Information'),
                _buildParagraph('We use the information we collect to:'),
                _buildBulletPoint('Create and manage your account and bookings on the Platform.'),
                _buildBulletPoint('Connect clients with salons and beauty professionals.'),
                _buildBulletPoint('Send booking confirmations, reminders, and receipts via SMS or email.'),
                _buildBulletPoint('Process payments and issue refunds when applicable.'),
                _buildBulletPoint('Provide salon owners with business analytics, staff management tools, POS features, and inventory reports.'),
                _buildBulletPoint('Improve and personalize your experience on the Platform.'),
                _buildBulletPoint('Respond to your support requests and inquiries.'),
                _buildBulletPoint('Send promotional communications (only with your consent; you may opt out at any time).'),
                _buildBulletPoint('Detect, prevent, and investigate fraud, abuse, or security incidents.'),
                _buildBulletPoint('Comply with applicable laws and regulatory obligations.'),

                _buildSectionTitle('3. Sharing Your Information'),
                _buildParagraph('We do not sell your personal data. We may share your information only in the following circumstances:'),
                _buildBulletPoint('With Salons: When you book an appointment, relevant booking details (name, phone, service selected) are shared with the salon you booked with.'),
                _buildBulletPoint('Service Providers: We work with trusted third-party vendors (hosting, payment processing, SMS delivery, analytics) who process data on our behalf under strict data protection agreements.'),
                _buildBulletPoint('Legal Compliance: We may disclose information if required by law, court order, or government authority in Canada or other applicable jurisdictions.'),
                _buildBulletPoint('Business Transfers: In the event of a merger, acquisition, or sale of assets, your data may be transferred. We will notify you before such a transfer takes effect.'),
                _buildBulletPoint('With Your Consent: For any other purpose, we will ask for your explicit consent before sharing.'),

                _buildSectionTitle('4. Data Retention'),
                _buildParagraph('We retain your personal data for as long as your account is active or as needed to provide you with our services. If you delete your account, we will delete or anonymize your personal data within 30 days, except where we are required to retain it for legal, tax, or accounting obligations. Anonymized and aggregated data may be retained indefinitely for analytical purposes.'),

                _buildSectionTitle('5. Data Security'),
                _buildParagraph('We take the security of your data seriously and implement industry-standard measures to protect it:'),
                _buildBulletPoint('All data is transmitted over HTTPS (TLS encryption).'),
                _buildBulletPoint('Passwords are stored using strong one-way hashing (bcrypt).'),
                _buildBulletPoint('Databases are access-controlled and regularly backed up.'),
                _buildBulletPoint('We conduct regular security reviews and vulnerability assessments.'),
                _buildBulletPoint('Access to personal data is restricted to authorized personnel only.'),
                _buildParagraph('However, no method of transmission over the internet is 100% secure. While we strive to protect your data, we cannot guarantee absolute security. In the event of a data breach that affects your rights, we will notify you promptly in accordance with applicable law.'),

                _buildSectionTitle('6. Cookies & Tracking Technologies'),
                _buildParagraph('We use cookies to maintain your session, remember your preferences (e.g., language), and understand how the Platform is used. You can control cookie settings through your browser. Note that disabling certain cookies may affect the functionality of the Platform.'),
                _buildBulletPoint('Essential Cookies: Required for the Platform to function (login sessions, CSRF protection).'),
                _buildBulletPoint('Preference Cookies: Remember your language and display settings.'),
                _buildBulletPoint('Analytics Cookies: Help us understand usage patterns to improve the Platform.'),

                _buildSectionTitle('7. Your Rights'),
                _buildParagraph('You have the following rights regarding your personal data:'),
                _buildBulletPoint('Access: Request a copy of the personal data we hold about you.'),
                _buildBulletPoint('Correction: Request correction of inaccurate or incomplete data.'),
                _buildBulletPoint('Deletion: Request deletion of your account and personal data ("right to be forgotten").'),
                _buildBulletPoint('Objection: Object to certain uses of your data, including direct marketing.'),
                _buildBulletPoint('Data Portability: Request your data in a structured, machine-readable format.'),
                _buildBulletPoint('Withdraw Consent: Where processing is based on consent, you may withdraw it at any time.'),
                _buildParagraph('To exercise any of these rights, please contact us at info@salojoy.com. We will respond within 30 days.'),

                _buildSectionTitle('8. Children\'s Privacy'),
                _buildParagraph('Salojoy is not directed to children under the age of 13. We do not knowingly collect personal data from children. If you believe a child has provided us with personal information, please contact us immediately and we will delete it.'),

                _buildSectionTitle('9. Changes to This Policy'),
                _buildParagraph('We may update this Privacy Policy from time to time. When we make material changes, we will notify you by email or by posting a prominent notice on the Platform before the change becomes effective. The "Last Updated" date at the top of this page will always reflect the most recent revision. Continued use of the Platform after changes are posted constitutes your acceptance of the updated policy.'),

                _buildSectionTitle('10. Contact Us'),
                _buildParagraph('If you have any questions, concerns, or requests regarding this Privacy Policy or how we handle your data, please reach out to us:'),
                _buildParagraph('SaloJoy\nVancouver, Canada\nEmail: info@salojoy.com\nPhone: +1 778 804 9199'),
              ],
              
              const SizedBox(height: 16),
              const Divider(color: Color(0xFFE5E7EB)),
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
