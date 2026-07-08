import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'saved_salons_screen.dart';
import '../../../../core/colors/app_color.dart';
import '../../../home/data/model/salon_model.dart';
import 'about_us_screen_view.dart';
import 'contact_screen_view.dart';
import 'forgot_password_screen_view.dart';
import 'login_screen_view.dart';
import 'privacy_policy_screen_view.dart';
import 'signup_screen_view.dart';
import 'terms_and_conditions_screen_view.dart';
import 'change_password_screen_view.dart';
import '../../../home/presentation/controller/home_controller.dart';
import '../../../../core/network/api_service.dart';
import '../../../../core/network/session_manager.dart';
import '../../../../core/common/widgets/custom_snackbar.dart';

class ProfileScreenView extends StatefulWidget {
  const ProfileScreenView({super.key});

  static bool isLoggedIn = false;
  static String? userToken;
  static Map<String, dynamic>? userData;

  @override
  State<ProfileScreenView> createState() => _ProfileScreenViewState();
}

class _ProfileScreenViewState extends State<ProfileScreenView> {
  // Toggle this to simulate login/logout state.
  // Replace with real auth state (e.g. GetX AuthController) later.
  bool get _isLoggedIn => ProfileScreenView.userToken != null;
  set _isLoggedIn(bool val) {
    if (!val) {
      ProfileScreenView.userToken = null;
      ProfileScreenView.userData = null;
      try {
        Get.find<ApiService>().setToken(null);
      } catch (_) {}
      // Clear persistent storage
      SessionManager.clearSession();
    } else {
      ProfileScreenView.isLoggedIn = true;
    }
  }

  // ── Login handler ──────────────────────────────────────────────
  void _handleLogin(String email, String password) {
    // TODO: call real auth API here
    if (email.isNotEmpty && password.isNotEmpty) {
      setState(() => _isLoggedIn = true);
    }
  }

  // ── Logout dialog ──────────────────────────────────────────────
  void _showLogoutDialog(BuildContext context) {
    showDialog(
      context: context,
      barrierDismissible: true,
      barrierColor: Colors.black.withOpacity(0.5),
      builder: (_) => Dialog(
        backgroundColor: Colors.transparent,
        insetPadding: const EdgeInsets.symmetric(horizontal: 28),
        child: Container(
          padding: const EdgeInsets.all(28),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.12),
                blurRadius: 30,
                offset: const Offset(0, 10),
              ),
            ],
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 72,
                height: 72,
                decoration: BoxDecoration(
                  color: AppColor.error.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.logout_rounded,
                  color: AppColor.error,
                  size: 34,
                ),
              ),
              const SizedBox(height: 20),
              Text(
                'Log Out?',
                style: GoogleFonts.poppins(
                  fontSize: 20,
                  fontWeight: FontWeight.w700,
                  color: AppColor.textPrimary,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                'Are you sure you want to log out of your SaloJoy account?',
                textAlign: TextAlign.center,
                style: GoogleFonts.poppins(
                  fontSize: 13,
                  color: AppColor.textSecondary,
                  height: 1.5,
                ),
              ),
              const SizedBox(height: 28),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Navigator.of(context).pop(),
                      style: OutlinedButton.styleFrom(
                        side: BorderSide(
                          color: AppColor.primary.withOpacity(0.4),
                        ),
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14),
                        ),
                      ),
                      child: Text(
                        'Cancel',
                        style: GoogleFonts.poppins(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: AppColor.primary,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () async {
                        Navigator.of(context).pop();
                        setState(() => _isLoggedIn = false);
                        CustomSnackbar.showSuccess(
                          title: 'Logged Out',
                          message: 'Logged out successfully',
                        );
                        final result = await Get.to(
                          () => const LoginScreenView(),
                        );
                        if (result == true) {
                          setState(() {
                            _isLoggedIn = true;
                          });
                          Get.find<HomeController>().currentIndex.value = 0;
                        }
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColor.error,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14),
                        ),
                        elevation: 0,
                      ),
                      child: Text(
                        'Log Out',
                        style: GoogleFonts.poppins(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: Colors.white,
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

  Widget _divider() => const Divider(
    height: 1,
    indent: 56,
    endIndent: 16,
    color: Color(0xFFF5F3FF),
  );

  // ─────────────────────────────────────────────────────────────────────
  //  BUILD
  // ─────────────────────────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    return _isLoggedIn ? _buildProfile() : _buildLoggedOutProfile();
  }

  // ─────────────────────────────────────────────────────────────────────
  //  NOT LOGGED IN — Logged Out Profile
  // ─────────────────────────────────────────────────────────────────────
  Widget _buildLoggedOutProfile() {
    return Scaffold(
      backgroundColor: AppColor.background,
      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            children: [
              // ── Purple Header ───────────────────────────────────────
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(24, 48, 24, 40),
                decoration: const BoxDecoration(color: Colors.transparent),
                child: Column(
                  children: [
                    // Lock icon in circle
                    Container(
                      width: 84,
                      height: 84,
                      decoration: BoxDecoration(
                        color: AppColor.primaryLight,
                        shape: BoxShape.circle,
                        border: Border.all(
                          color: AppColor.primary.withOpacity(0.2),
                          width: 2,
                        ),
                      ),
                      child: const Icon(
                        Icons.person_outline,
                        color: AppColor.primary,
                        size: 42,
                      ),
                    ),
                    const SizedBox(height: 18),
                    Text(
                      'Welcome to PickHandy',
                      style: GoogleFonts.poppins(
                        color: AppColor.textPrimary,
                        fontSize: 22,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'Sign in to manage your bookings,\nwallet & profile',
                      textAlign: TextAlign.center,
                      style: GoogleFonts.poppins(
                        color: AppColor.textSecondary,
                        fontSize: 13,
                        height: 1.5,
                      ),
                    ),
                    const SizedBox(height: 24),
                    SizedBox(
                      width: 200,
                      child: ElevatedButton(
                        onPressed: () async {
                          final result = await Get.to(
                            () => const LoginScreenView(),
                          );
                          if (result == true) {
                            setState(() {
                              _isLoggedIn = true;
                            });
                            // Switch tab to Home Screen
                            Get.find<HomeController>().currentIndex.value = 0;
                          }
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColor.primary,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                          elevation: 0,
                        ),
                        child: Text(
                          'Sign In / Sign Up',
                          style: GoogleFonts.poppins(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 32),

              // ── Common Menus (Settings, About, Support, Legal) ─────────────
              _buildCommonMenus(context, isLoggedIn: false),

              const SizedBox(height: 32),

              Text(
                'SaloJoy v1.0.0',
                style: GoogleFonts.poppins(
                  fontSize: 12,
                  color: AppColor.textHint,
                ),
              ),

              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  // ─────────────────────────────────────────────────────────────────────
  //  LOGGED IN — Full Profile
  // ─────────────────────────────────────────────────────────────────────
  Widget _buildProfile() {
    return Scaffold(
      backgroundColor: AppColor.background,
      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            children: [
              // ── Header purple gradient ─────────────────────────
              Obx(() {
                final controller = Get.find<HomeController>();
                final userData = controller.userData.value;
                final isLoggedIn = userData != null;

                final name = isLoggedIn
                    ? (userData['name'] ?? 'User')
                    : MockData.userFullName;
                final contact = isLoggedIn
                    ? (userData['contact'] ?? '')
                    : MockData.userPhone;
                final photo = isLoggedIn ? userData['photo'] : null;

                final parts = name.split(' ');
                String initials = parts
                    .map((p) => p.isNotEmpty ? p[0] : '')
                    .join('')
                    .toUpperCase();
                if (initials.length > 2) initials = initials.substring(0, 2);
                if (initials.isEmpty) initials = 'U';

                return Container(
                  width: double.infinity,
                  padding: const EdgeInsets.fromLTRB(20, 24, 20, 32),
                  child: Column(
                    children: [
                      Stack(
                        children: [
                          CircleAvatar(
                            radius: 44,
                            backgroundColor: AppColor.primaryLight,
                            child: (photo != null && photo.isNotEmpty)
                                ? ClipOval(
                                    child: Image.network(
                                      '${ApiService.mediaBaseUrl}$photo',
                                      width: 88,
                                      height: 88,
                                      fit: BoxFit.cover,
                                      errorBuilder: (_, __, ___) => Text(
                                        initials,
                                        style: GoogleFonts.poppins(
                                          color: AppColor.primary,
                                          fontSize: 26,
                                          fontWeight: FontWeight.w700,
                                        ),
                                      ),
                                    ),
                                  )
                                : Text(
                                    initials,
                                    style: GoogleFonts.poppins(
                                      color: AppColor.primary,
                                      fontSize: 26,
                                      fontWeight: FontWeight.w700,
                                    ),
                                  ),
                          ),
                          Positioned(
                            bottom: 0,
                            right: 0,
                            child: Container(
                              width: 26,
                              height: 26,
                              decoration: BoxDecoration(
                                color: AppColor.primary,
                                shape: BoxShape.circle,
                                border: Border.all(
                                  color: Colors.white,
                                  width: 2,
                                ),
                              ),
                              child: const Icon(
                                Icons.camera_alt,
                                size: 12,
                                color: Colors.white,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 14),
                      Text(
                        name,
                        style: GoogleFonts.poppins(
                          fontSize: 20,
                          fontWeight: FontWeight.w700,
                          color: AppColor.textPrimary,
                        ),
                      ),
                      if (contact.isNotEmpty) ...[
                        const SizedBox(height: 4),
                        Text(
                          contact,
                          style: GoogleFonts.poppins(
                            fontSize: 13,
                            color: AppColor.textSecondary,
                          ),
                        ),
                      ],
                      const SizedBox(height: 14),
                      GestureDetector(
                        onTap: () {},
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 20,
                            vertical: 8,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(
                              color: AppColor.primary.withOpacity(0.5),
                            ),
                          ),
                          child: Text(
                            'Edit Profile',
                            style: GoogleFonts.poppins(
                              fontSize: 13,
                              fontWeight: FontWeight.w500,
                              color: AppColor.primary,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                );
              }),

              // ── Menu Group 1 ──────────────────────────────────
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16),
                child: Container(
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(16),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.04),
                        blurRadius: 12,
                        offset: const Offset(0, 2),
                      ),
                    ],
                  ),
                  child: Column(
                    children: [
                      _MenuItem(
                        icon: Icons.calendar_month_outlined,
                        iconColor: AppColor.primary,
                        iconBg: AppColor.primaryLight,
                        label: 'My Bookings',
                        onTap: () {
                          Get.find<HomeController>().currentIndex.value = 1;
                        },
                      ),
                    ],
                  ),
                ),
              ),

              const SizedBox(height: 12),

              // ── Common Menus (Settings, About, Support, Legal) ─────────────
              _buildCommonMenus(context, isLoggedIn: true),

              const SizedBox(height: 32),

              Text(
                'PickHandy v1.0.0',
                style: GoogleFonts.poppins(
                  fontSize: 12,
                  color: AppColor.textHint,
                ),
              ),

              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  // ─────────────────────────────────────────────────────────────────────
  //  COMMON MENUS (Visible in both states)
  // ─────────────────────────────────────────────────────────────────────
  Widget _buildCommonMenus(BuildContext context, {bool isLoggedIn = false}) {
    return Column(
      children: [
        // ── Info Menu ───────────────────────────────────────
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.04),
                  blurRadius: 12,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Column(
              children: [
                _MenuItem(
                  icon: Icons.favorite_border,
                  iconColor: const Color(0xFFE11D48),
                  iconBg: const Color(0xFFFFE4E6),
                  label: 'Saved',
                  onTap: () => Get.to(() => const SavedSalonsScreen()),
                ),
                _divider(),

                _MenuItem(
                  icon: Icons.featured_play_list_outlined,
                  iconColor: const Color(0xFF06B6D4),
                  iconBg: const Color(0xFFCFFAFE),
                  label: 'Features',
                  onTap: () {
                    // Get.to(() => const FeaturesScreenView());
                  },
                ),
              ],
            ),
          ),
        ),

        const SizedBox(height: 12),

        // ── Support & About ───────────────────────────────────────
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.04),
                  blurRadius: 12,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Column(
              children: [
                _MenuItem(
                  icon: Icons.business_outlined,
                  iconColor: const Color(0xFF3B82F6),
                  iconBg: const Color(0xFFDBEAFE),
                  label: 'About Us',
                  onTap: () => Get.to(() => const AboutUsScreenView()),
                ),
                if (isLoggedIn) ...[
                  _divider(),
                  _MenuItem(
                    icon: Icons.confirmation_number_outlined,
                    iconColor: const Color(0xFF10B981),
                    iconBg: const Color(0xFFD1FAE5),
                    label: 'Tickets',
                    onTap: () {
                      // Get.to(() => const TicketsListScreenView());
                    },
                  ),
                ],
                _divider(),
                _MenuItem(
                  icon: Icons.headset_mic_outlined,
                  iconColor: const Color(0xFF8B5CF6),
                  iconBg: const Color(0xFFEDE9FE),
                  label: 'Contact',
                  onTap: () => Get.to(() => const ContactScreenView()),
                ),
              ],
            ),
          ),
        ),

        const SizedBox(height: 12),

        // ── Legal & Settings ───────────────────────────────────────
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.04),
                  blurRadius: 12,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Column(
              children: [
                if (isLoggedIn) ...[
                  _MenuItem(
                    icon: Icons.lock_outline,
                    iconColor: AppColor.textSecondary,
                    iconBg: const Color(0xFFF3F4F6),
                    label: 'Change Password',
                    onTap: () => Get.to(() => const ChangePasswordScreenView()),
                  ),
                  _divider(),
                ],
                _MenuItem(
                  icon: Icons.privacy_tip_outlined,
                  iconColor: AppColor.textSecondary,
                  iconBg: const Color(0xFFF3F4F6),
                  label: 'Privacy Policy',
                  onTap: () => Get.to(() => const PrivacyPolicyScreenView()),
                ),
                _divider(),
                _MenuItem(
                  icon: Icons.gavel_outlined,
                  iconColor: AppColor.textSecondary,
                  iconBg: const Color(0xFFF3F4F6),
                  label: 'Terms & Conditions',
                  onTap: () {
                    // Get.to(() => const TermsAndConditionsScreenView());
                  },
                ),
                if (isLoggedIn) ...[
                  _divider(),
                  _MenuItem(
                    icon: Icons.logout,
                    iconColor: AppColor.error,
                    iconBg: AppColor.error.withOpacity(0.1),
                    label: 'Log Out',
                    labelColor: AppColor.error,
                    showArrow: false,
                    onTap: () => _showLogoutDialog(context),
                  ),
                ],
              ],
            ),
          ),
        ),
      ],
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────
//  Menu Item widget
// ─────────────────────────────────────────────────────────────────────────
class _MenuItem extends StatelessWidget {
  final IconData icon;
  final Color iconColor;
  final Color iconBg;
  final String label;
  final Color? labelColor;
  final Widget? trailing;
  final bool showArrow;
  final VoidCallback onTap;

  const _MenuItem({
    required this.icon,
    required this.iconColor,
    required this.iconBg,
    required this.label,
    this.labelColor,
    this.trailing,
    this.showArrow = true,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      behavior: HitTestBehavior.opaque,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        child: Row(
          children: [
            Container(
              width: 38,
              height: 38,
              decoration: BoxDecoration(
                color: iconBg,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, color: iconColor, size: 20),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Text(
                label,
                style: GoogleFonts.poppins(
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                  color: labelColor ?? AppColor.textPrimary,
                ),
              ),
            ),
            if (trailing != null) trailing!,
            if (showArrow) ...[
              const SizedBox(width: 8),
              const Icon(
                Icons.arrow_forward_ios,
                size: 14,
                color: AppColor.textHint,
              ),
            ],
          ],
        ),
      ),
    );
  }
}
