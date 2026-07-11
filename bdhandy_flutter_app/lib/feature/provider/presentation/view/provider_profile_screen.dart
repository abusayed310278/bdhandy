import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../../booking/presentation/view/book_service_screen.dart';
import '../../../profile/presentation/view/login_screen_view.dart';
import '../../../profile/presentation/view/signup_screen_view.dart';

class ProviderProfileScreen extends StatefulWidget {
  const ProviderProfileScreen({super.key});

  @override
  State<ProviderProfileScreen> createState() => _ProviderProfileScreenState();
}

class _ProviderProfileScreenState extends State<ProviderProfileScreen> {
  final GlobalKey _aboutKey = GlobalKey();
  final GlobalKey _servicesKey = GlobalKey();
  final GlobalKey _hoursKey = GlobalKey();
  final GlobalKey _reviewsKey = GlobalKey();
  final GlobalKey _galleryKey = GlobalKey();

  String _activeTab = 'About';

  void _scrollToSection(GlobalKey key, String tabName) {
    setState(() {
      _activeTab = tabName;
    });
    if (key.currentContext != null) {
      Scrollable.ensureVisible(
        key.currentContext!,
        duration: const Duration(milliseconds: 500),
        curve: Curves.easeInOut,
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xffF9FAFB),
      body: CustomScrollView(
        slivers: [
          // Using a SliverToBoxAdapter for the entire header to easily manage the overlapping profile picture
          SliverToBoxAdapter(
            child: _buildHeader(context),
          ),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 16),
                  _buildProfileInfo(),
                  const SizedBox(height: 24),
                  _buildStatsBox(),
                  const SizedBox(height: 24),
                  _buildTabsRow(),
                  const SizedBox(height: 24),
                  Container(key: _aboutKey, child: _buildAboutSection()),
                  const SizedBox(height: 24),
                  Container(key: _servicesKey, child: _buildServicesOffered()),
                  const SizedBox(height: 24),
                  Container(key: _galleryKey, child: _buildWorkGallery()),
                  const SizedBox(height: 24),
                  _buildHoursAndReviews(hoursKey: _hoursKey, reviewsKey: _reviewsKey),
                  const SizedBox(height: 120), // Padding for bottom nav
                ],
              ),
            ),
          ),
        ],
      ),
      bottomSheet: _buildBottomNav(),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return SizedBox(
      height: 260,
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          // Cover Photo
          Container(
            height: 200,
            width: double.infinity,
            decoration: BoxDecoration(
              color: Colors.grey.shade300,
              image: const DecorationImage(
                image: AssetImage('assets/images/handyman.png'), // Placeholder
                fit: BoxFit.cover,
              ),
            ),
            // Fallback for placeholder if image doesn't exist
            child: Container(color: Colors.grey.shade200),
          ),
          // Top Action Buttons
          SafeArea(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  _buildCircularIconButton(Icons.arrow_back_ios_new, () => Get.back()),
                  Row(
                    children: [
                      _buildCircularIconButton(Icons.favorite_border, () {}),
                      const SizedBox(width: 12),
                      _buildCircularIconButton(Icons.share_outlined, () {}),
                      const SizedBox(width: 12),
                      _buildCircularIconButton(Icons.more_horiz, () {}),
                    ],
                  )
                ],
              ),
            ),
          ),
          // Profile Picture Overlapping
          Positioned(
            left: 16,
            bottom: 0,
            child: Container(
              padding: const EdgeInsets.all(4),
              decoration: const BoxDecoration(
                color: Color(0xffF9FAFB),
                shape: BoxShape.circle,
              ),
              child: Stack(
                children: [
                  const CircleAvatar(
                    radius: 46,
                    backgroundColor: Colors.grey,
                    backgroundImage: AssetImage('assets/images/handyman.png'), // Placeholder
                  ),
                  Positioned(
                    bottom: 0,
                    right: 0,
                    child: Container(
                      padding: const EdgeInsets.all(2),
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(
                        Icons.verified,
                        color: Color(0xff16B83E),
                        size: 24,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCircularIconButton(IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(
          color: Colors.white,
          shape: BoxShape.circle,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 10,
            ),
          ],
        ),
        child: Icon(icon, color: Colors.black87, size: 20),
      ),
    );
  }

  Widget _buildProfileInfo() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            _buildTag('EN'),
            const SizedBox(width: 8),
            _buildTagIcon(Icons.g_translate), // Assuming it's a translation/verified badge
          ],
        ),
        const SizedBox(height: 8),
        const Text(
          'Deepak Sharma',
          style: TextStyle(
            fontSize: 26,
            fontWeight: FontWeight.w800,
            color: Color(0xff111827),
          ),
        ),
        const SizedBox(height: 4),
        Text(
          'Interior design consultant\nfor modern UAE homes',
          style: TextStyle(
            fontSize: 14,
            color: Colors.grey.shade700,
            height: 1.4,
          ),
        ),
        const SizedBox(height: 16),
        Row(
          children: [
            const Icon(Icons.star, color: Colors.amber, size: 18),
            const SizedBox(width: 4),
            const Text(
              '4.6',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
            ),
            const SizedBox(width: 4),
            Text(
              '(5 reviews)',
              style: TextStyle(color: Colors.grey.shade600, fontSize: 13),
            ),
            _buildVerticalDivider(),
            Icon(Icons.location_on_outlined, color: Colors.grey.shade500, size: 16),
            const SizedBox(width: 4),
            Text(
              'Sakamkam, Fujairah City',
              style: TextStyle(color: Colors.grey.shade600, fontSize: 13),
            ),
          ],
        ),
        const SizedBox(height: 8),
        Row(
          children: [
            Text(
              '8 yrs experience',
              style: TextStyle(color: Colors.grey.shade600, fontSize: 13),
            ),
            const Spacer(),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
              decoration: BoxDecoration(
                color: Colors.blue.shade50,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Text(
                'Expert',
                style: TextStyle(
                  color: Colors.blue.shade700,
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                ),
              ),
            )
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Container(
              width: 8,
              height: 8,
              decoration: const BoxDecoration(
                color: Color(0xff16B83E),
                shape: BoxShape.circle,
              ),
            ),
            const SizedBox(width: 6),
            const Text(
              'Open today',
              style: TextStyle(
                color: Color(0xff16B83E),
                fontWeight: FontWeight.bold,
                fontSize: 13,
              ),
            ),
            const SizedBox(width: 8),
            Text(
              '09:00 - 18:00',
              style: TextStyle(
                color: Colors.grey.shade600,
                fontSize: 13,
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildVerticalDivider() {
    return Container(
      height: 12,
      width: 1,
      color: Colors.grey.shade300,
      margin: const EdgeInsets.symmetric(horizontal: 12),
    );
  }

  Widget _buildTag(String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(6),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Text(
        text,
        style: TextStyle(
          color: Colors.blue.shade700,
          fontWeight: FontWeight.bold,
          fontSize: 11,
        ),
      ),
    );
  }

  Widget _buildTagIcon(IconData icon) {
    return Container(
      padding: const EdgeInsets.all(4),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(6),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Icon(
        icon,
        color: Colors.blue.shade700,
        size: 14,
      ),
    );
  }

  Widget _buildStatsBox() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 24),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: [
          _buildStatItem(Icons.sentiment_satisfied_alt, '8+', 'YEARS EXP.'),
          _buildStatDivider(),
          _buildStatItem(Icons.star_outline, '4.6', 'RATING', iconColor: Colors.amber, valueColor: Colors.orange.shade700),
          _buildStatDivider(),
          _buildStatItem(Icons.image_outlined, '5', 'REVIEWS', iconColor: Colors.blue, valueColor: Colors.blue.shade700),
          _buildStatDivider(),
          _buildStatItem(Icons.work_outline, '3', 'SERVICES'),
        ],
      ),
    );
  }

  Widget _buildStatDivider() {
    return Container(
      width: 1,
      height: 40,
      color: Colors.grey.shade200,
    );
  }

  Widget _buildStatItem(IconData icon, String value, String label, {Color iconColor = Colors.blue, Color valueColor = Colors.blue}) {
    return Column(
      children: [
        Row(
          children: [
            Icon(icon, color: iconColor, size: 20),
            const SizedBox(width: 4),
            Text(
              value,
              style: TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.w800,
                color: valueColor,
              ),
            ),
          ],
        ),
        const SizedBox(height: 6),
        Text(
          label,
          style: TextStyle(
            fontSize: 10,
            fontWeight: FontWeight.bold,
            color: Colors.grey.shade500,
            letterSpacing: 0.5,
          ),
        ),
      ],
    );
  }

  Widget _buildTabsRow() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
      ),
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Row(
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            const SizedBox(width: 16),
            _buildTabItem(Icons.info_outline, 'About', _aboutKey),
            const SizedBox(width: 16),
            _buildTabItem(Icons.file_copy_outlined, 'Services', _servicesKey),
            const SizedBox(width: 16),
            _buildTabItem(Icons.access_time, 'Hours', _hoursKey),
            const SizedBox(width: 16),
            _buildTabItem(Icons.chat_bubble_outline, 'Reviews', _reviewsKey),
            const SizedBox(width: 16),
            _buildTabItem(Icons.image_outlined, 'Gallery', _galleryKey),
            const SizedBox(width: 16),
          ],
        ),
      ),
    );
  }

  Widget _buildTabItem(IconData icon, String label, GlobalKey key) {
    bool isActive = _activeTab == label;
    return GestureDetector(
      onTap: () => _scrollToSection(key, label),
      child: Column(
        children: [
          Row(
            children: [
              Icon(
                icon,
                size: 16,
                color: isActive ? Colors.blue.shade700 : Colors.grey.shade600,
              ),
              const SizedBox(width: 6),
              Text(
                label,
                style: TextStyle(
                  fontWeight: isActive ? FontWeight.bold : FontWeight.w600,
                  color: isActive ? Colors.blue.shade700 : Colors.grey.shade600,
                  fontSize: 13,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          if (isActive)
            Container(
              height: 2,
              width: 60,
              color: Colors.blue.shade700,
            )
          else
            const SizedBox(height: 2),
        ],
      ),
    );
  }

  Widget _buildAboutSection() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'About',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: Color(0xff111827),
            ),
          ),
          const SizedBox(height: 12),
          Text(
            'Interior design consultant with a portfolio of 200+ projects across the UAE. I blend contemporary design with Arabian aesthetics to create beautiful, functional living spaces. Offering full design consultation, mood boards, and project management.',
            style: TextStyle(
              fontSize: 13,
              color: Colors.grey.shade600,
              height: 1.5,
            ),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.grey.shade100,
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Text(
                  'SPECIALTIES',
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.bold,
                    color: Colors.grey.shade600,
                  ),
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  'Interior design consultant for modern UAE homes',
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                    color: Colors.blue.shade700,
                  ),
                ),
              ),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildServicesOffered() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Services Offered',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: Color(0xff111827),
                ),
              ),
              Text(
                'View all',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: Colors.blue.shade700,
                ),
              )
            ],
          ),
          const SizedBox(height: 16),
          _buildServiceCard(
            title: 'Interior Design Consultation',
            desc: 'Professional interior design consultation service with guaranteed quality.',
            duration: '1 hr',
            price: 'AED188',
          ),
          const Divider(height: 24),
          _buildServiceCard(
            title: 'Interior Painting',
            desc: 'Professional interior painting service with guaranteed quality.',
            duration: '3-10 hr',
            price: 'AED755',
          ),
          const Divider(height: 24),
          _buildServiceCard(
            title: 'Wallpaper Installation',
            desc: 'Professional wallpaper installation service with guaranteed quality.',
            duration: '1-10 hr',
            price: 'AED404',
          ),
        ],
      ),
    );
  }

  Widget _buildServiceCard({required String title, required String desc, required String duration, required String price}) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 80,
          height: 60,
          decoration: BoxDecoration(
            color: Colors.grey.shade200,
            borderRadius: BorderRadius.circular(8),
            image: const DecorationImage(
              image: AssetImage('assets/images/handyman.png'), // placeholder
              fit: BoxFit.cover,
            ),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: Color(0xff111827),
                ),
              ),
              const SizedBox(height: 4),
              Text(
                desc,
                style: TextStyle(
                  fontSize: 11,
                  color: Colors.grey.shade600,
                  height: 1.3,
                ),
              ),
              const SizedBox(height: 6),
              Row(
                children: [
                  Icon(Icons.access_time, size: 12, color: Colors.grey.shade500),
                  const SizedBox(width: 4),
                  Text(
                    duration,
                    style: TextStyle(fontSize: 11, color: Colors.grey.shade600),
                  ),
                ],
              ),
            ],
          ),
        ),
        const SizedBox(width: 12),
        Column(
          crossAxisAlignment: CrossAxisAlignment.end,
          children: [
            Text(
              price,
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w900,
                color: Color(0xff111827),
              ),
            ),
            Text(
              'Fixed Price',
              style: TextStyle(
                fontSize: 10,
                color: Colors.grey.shade500,
              ),
            ),
            const SizedBox(height: 8),
            SizedBox(
              height: 28,
              width: 70,
              child: ElevatedButton(
                onPressed: () => Get.to(() => const BookServiceScreen()),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.blue.shade700,
                  padding: EdgeInsets.zero,
                  elevation: 0,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(6),
                  ),
                ),
                child: const Text(
                  'Book',
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            )
          ],
        )
      ],
    );
  }

  Widget _buildWorkGallery() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Work Gallery',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: Color(0xff111827),
                ),
              ),
              Text(
                'View all',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: Colors.blue.shade700,
                ),
              )
            ],
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: Container(
                  height: 80,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade200,
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Container(
                  height: 80,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade300,
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Container(
                  height: 80,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade200,
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Container(
                  height: 80,
                  decoration: BoxDecoration(
                    color: const Color(0xffF9FAFB),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: Colors.grey.shade200),
                  ),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.image_outlined, color: Colors.grey.shade500, size: 20),
                      const SizedBox(height: 4),
                      Text(
                        '+12',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: Colors.grey.shade600,
                        ),
                      )
                    ],
                  ),
                ),
              ),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildHoursAndReviews({required GlobalKey hoursKey, required GlobalKey reviewsKey}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        Container(
          key: hoursKey,
          padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Icon(Icons.access_time, color: Colors.blue.shade700, size: 18),
                    const SizedBox(width: 8),
                    const Expanded(
                      child: Text(
                        'Business Hours',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                _buildHourRow('Saturday', '09:00 - 18:00'),
                const SizedBox(height: 12),
                _buildHourRow('Sunday', '09:00 - 18:00'),
                const SizedBox(height: 12),
                _buildHourRow('Monday', '09:00 - 18:00'),
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 8),
                  decoration: BoxDecoration(
                    color: Colors.blue.shade50,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Text(
                            'Thursday',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: Colors.blue.shade700,
                            ),
                          ),
                          const SizedBox(width: 2),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 2, vertical: 2),
                            decoration: BoxDecoration(
                              color: Colors.blue.shade700,
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: const Text(
                              'Today',
                              style: TextStyle(
                                fontSize: 8,
                                color: Colors.white,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          )
                        ],
                      ),
                      Text(
                        '09:00 - 18:00',
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          color: Colors.blue.shade700,
                        ),
                      ),
                    ],
                  ),
                )
              ],
            ),
          ),
        const SizedBox(height: 24),
        Container(
          key: reviewsKey,
          padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.blue.shade100),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Row(
                        children: [
                          Icon(Icons.verified_user_outlined, color: Colors.blue.shade700, size: 18),
                          const SizedBox(width: 4),
                          const Expanded(
                            child: Text(
                              'Reviews',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Text(
                      'View all',
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: Colors.blue.shade700,
                      ),
                    )
                  ],
                ),
                const SizedBox(height: 16),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    const Text(
                      '4.6',
                      style: TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.w900,
                        height: 1,
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildRatingBarRow('5', 0.8),
                          _buildRatingBarRow('4', 0.3),
                          _buildRatingBarRow('3', 0.1),
                        ],
                      ),
                    )
                  ],
                ),
                const SizedBox(height: 4),
                Row(
                  children: [
                    const Icon(Icons.star, color: Colors.amber, size: 10),
                    const Icon(Icons.star, color: Colors.amber, size: 10),
                    const Icon(Icons.star, color: Colors.amber, size: 10),
                    const Icon(Icons.star, color: Colors.amber, size: 10),
                    const Icon(Icons.star_half, color: Colors.amber, size: 10),
                    const SizedBox(width: 4),
                    Text(
                      '5 REVIEWS',
                      style: TextStyle(
                        fontSize: 8,
                        color: Colors.grey.shade500,
                        fontWeight: FontWeight.bold,
                      ),
                    )
                  ],
                ),
                const SizedBox(height: 16),
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    border: Border.all(color: Colors.grey.shade200),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            padding: const EdgeInsets.all(6),
                            decoration: BoxDecoration(
                              color: Colors.grey.shade200,
                              shape: BoxShape.circle,
                            ),
                            child: const Text('V', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 10)),
                          ),
                          const SizedBox(width: 6),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    const Expanded(
                                      child: Text(
                                        'Vikram Al Mansouri',
                                        style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold),
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                    Text('1mo', style: TextStyle(fontSize: 8, color: Colors.grey.shade500)),
                                  ],
                                ),
                                const Row(
                                  children: [
                                    Icon(Icons.star, color: Colors.amber, size: 8),
                                    Icon(Icons.star, color: Colors.amber, size: 8),
                                    Icon(Icons.star, color: Colors.amber, size: 8),
                                    Icon(Icons.star, color: Colors.amber, size: 8),
                                    Icon(Icons.star, color: Colors.amber, size: 8),
                                  ],
                                ),
                              ],
                            ),
                          )
                        ],
                      ),
                      const SizedBox(height: 6),
                      Text(
                        'Excellent service! Very professional and on time. Highly recommend to anyone in Dubai.',
                        style: TextStyle(
                          fontSize: 9,
                          color: Colors.grey.shade700,
                          height: 1.3,
                        ),
                      )
                    ],
                  ),
                )
              ],
            ),
        ),
      ],
    );
  }

  Widget _buildHourRow(String day, String time) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          day,
          style: TextStyle(
            fontSize: 10,
            color: Colors.grey.shade700,
          ),
        ),
        Text(
          time,
          style: const TextStyle(
            fontSize: 10,
            fontWeight: FontWeight.w600,
            color: Color(0xff111827),
          ),
        ),
      ],
    );
  }

  Widget _buildRatingBarRow(String label, double percent) {
    return Row(
      children: [
        Text(label, style: TextStyle(fontSize: 8, color: Colors.grey.shade600)),
        const Icon(Icons.star, color: Colors.amber, size: 8),
        const SizedBox(width: 4),
        Expanded(
          child: Container(
            height: 4,
            decoration: BoxDecoration(
              color: Colors.grey.shade200,
              borderRadius: BorderRadius.circular(2),
            ),
            child: FractionallySizedBox(
              alignment: Alignment.centerLeft,
              widthFactor: percent,
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.blue.shade400,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
          ),
        )
      ],
    );
  }

  void _showContactInfo(BuildContext context) {
    Get.bottomSheet(
      Container(
        padding: const EdgeInsets.all(24),
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Contact Information',
                  style: TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                IconButton(
                  onPressed: () => Get.back(),
                  icon: const Icon(Icons.close),
                ),
              ],
            ),
            const SizedBox(height: 24),
            _buildContactRow(Icons.phone_outlined, 'Phone', '+971 50 123 4567'),
            const SizedBox(height: 16),
            _buildContactRow(Icons.email_outlined, 'Email', 'hello@deepakdesign.com'),
            const SizedBox(height: 16),
            _buildContactRow(Icons.language, 'Website', 'www.deepakdesign.com'),
            const SizedBox(height: 16),
            _buildContactRow(Icons.location_on_outlined, 'Address', 'Sakamkam, Fujairah City, UAE'),
            const SizedBox(height: 24),
          ],
        ),
      ),
      isScrollControlled: true,
    );
  }

  Widget _buildContactRow(IconData icon, String title, String value) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.blue.shade50,
            shape: BoxShape.circle,
          ),
          child: Icon(icon, color: Colors.blue.shade700, size: 20),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: TextStyle(
                  fontSize: 12,
                  color: Colors.grey.shade600,
                  fontWeight: FontWeight.w500,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: Color(0xff111827),
                ),
              ),
            ],
          ),
        )
      ],
    );
  }

  void _showSubmitRequestDialog(BuildContext context) {
    String? selectedService;
    String selectedTime = 'Any time';
    String selectedUrgency = 'Normal Priority';

    final titleController = TextEditingController();
    final descriptionController = TextEditingController();
    final addressController = TextEditingController();
    DateTime? selectedDate;

    Get.dialog(
      Dialog(
        insetPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 24),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
        ),
        child: StatefulBuilder(
          builder: (context, setDialogState) {
            return SingleChildScrollView(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    /// Header
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: Colors.blue.shade50,
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Icon(
                            Icons.content_paste_outlined,
                            color: Colors.blue.shade700,
                            size: 22,
                          ),
                        ),
                        const SizedBox(width: 12),
                        const Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Submit Service Request',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.w800,
                                  color: Color(0xff111827),
                                ),
                              ),
                              SizedBox(height: 2),
                              Text(
                                'To: Deepak Sharma',
                                style: TextStyle(
                                  fontSize: 13,
                                  color: Color(0xff64748B),
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          ),
                        ),
                        IconButton(
                          onPressed: () => Get.back(),
                          icon: const Icon(Icons.close, color: Color(0xff94A3B8)),
                        ),
                      ],
                    ),

                    const SizedBox(height: 22),

                    _fieldLabel('SERVICE REQUIRED'),
                    DropdownButtonFormField<String>(
                      value: selectedService,
                      hint: const Text('— Select a service (optional) —'),
                      items: const [
                        DropdownMenuItem(
                          value: 'Interior Design Consultation',
                          child: Text('Interior Design Consultation'),
                        ),
                        DropdownMenuItem(
                          value: 'Interior Painting',
                          child: Text('Interior Painting'),
                        ),
                        DropdownMenuItem(
                          value: 'Wallpaper Installation',
                          child: Text('Wallpaper Installation'),
                        ),
                      ],
                      onChanged: (value) {
                        setDialogState(() {
                          selectedService = value;
                        });
                      },
                      decoration: _inputDecoration(),
                    ),

                    const SizedBox(height: 16),

                    _fieldLabel('REQUEST TITLE'),
                    TextFormField(
                      controller: titleController,
                      decoration: _inputDecoration(
                        hint: 'e.g. Fix leaking pipe under kitchen sink',
                      ),
                    ),

                    const SizedBox(height: 16),

                    _fieldLabel('DESCRIPTION OF WORK'),
                    TextFormField(
                      controller: descriptionController,
                      maxLines: 4,
                      decoration: _inputDecoration(
                        hint: 'Describe what you need help with in detail...',
                      ),
                    ),

                    const SizedBox(height: 16),

                    Row(
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              _fieldLabel('PREFERRED DATE'),
                              InkWell(
                                onTap: () async {
                                  final pickedDate = await showDatePicker(
                                    context: context,
                                    initialDate: DateTime.now(),
                                    firstDate: DateTime.now(),
                                    lastDate: DateTime.now().add(
                                      const Duration(days: 365),
                                    ),
                                  );

                                  if (pickedDate != null) {
                                    setDialogState(() {
                                      selectedDate = pickedDate;
                                    });
                                  }
                                },
                                child: InputDecorator(
                                  decoration: _inputDecoration(),
                                  child: Row(
                                    mainAxisAlignment:
                                        MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(
                                        selectedDate == null
                                            ? 'mm/dd/yyyy'
                                            : '${selectedDate!.month.toString().padLeft(2, '0')}/${selectedDate!.day.toString().padLeft(2, '0')}/${selectedDate!.year}',
                                        style: TextStyle(
                                          color: selectedDate == null
                                              ? const Color(0xff94A3B8)
                                              : const Color(0xff111827),
                                        ),
                                      ),
                                      const Icon(
                                        Icons.calendar_today_outlined,
                                        size: 18,
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              _fieldLabel('PREFERRED TIME'),
                              DropdownButtonFormField<String>(
                                value: selectedTime,
                                items: const [
                                  DropdownMenuItem(
                                    value: 'Any time',
                                    child: Text('— Any time —'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'Morning',
                                    child: Text('Morning'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'Afternoon',
                                    child: Text('Afternoon'),
                                  ),
                                  DropdownMenuItem(
                                    value: 'Evening',
                                    child: Text('Evening'),
                                  ),
                                ],
                                onChanged: (value) {
                                  setDialogState(() {
                                    selectedTime = value!;
                                  });
                                },
                                decoration: _inputDecoration(),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 16),

                    _fieldLabel('URGENCY'),
                    DropdownButtonFormField<String>(
                      value: selectedUrgency,
                      items: const [
                        DropdownMenuItem(
                          value: 'Normal Priority',
                          child: Text('Normal Priority'),
                        ),
                        DropdownMenuItem(
                          value: 'High Priority',
                          child: Text('High Priority'),
                        ),
                        DropdownMenuItem(
                          value: 'Emergency',
                          child: Text('Emergency'),
                        ),
                      ],
                      onChanged: (value) {
                        setDialogState(() {
                          selectedUrgency = value!;
                        });
                      },
                      decoration: _inputDecoration(),
                    ),

                    const SizedBox(height: 16),

                    _fieldLabel('SERVICE ADDRESS'),
                    TextFormField(
                      controller: addressController,
                      decoration: _inputDecoration(
                        hint: 'Your street address',
                      ),
                    ),

                    const SizedBox(height: 16),

                    _fieldLabel('ATTACH PHOTOS / DOCUMENTS'),
                    Row(
                      children: [
                        ElevatedButton(
                          onPressed: () {
                            // Add file_picker package later if needed
                          },
                          style: ElevatedButton.styleFrom(
                            elevation: 0,
                            backgroundColor: Colors.blue.shade50,
                            foregroundColor: Colors.blue.shade700,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(20),
                            ),
                          ),
                          child: const Text(
                            'Choose Files',
                            style: TextStyle(fontWeight: FontWeight.bold),
                          ),
                        ),
                        const SizedBox(width: 12),
                        const Text(
                          'No file chosen',
                          style: TextStyle(
                            fontSize: 13,
                            color: Color(0xff64748B),
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 20),

                    Divider(color: Colors.grey.shade200),

                    const SizedBox(height: 16),

                    Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        TextButton(
                          onPressed: () => Get.back(),
                          child: const Text(
                            'Cancel',
                            style: TextStyle(
                              color: Color(0xff64748B),
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        ElevatedButton(
                          onPressed: () {
                            // Submit request API call here
                            Get.back();

                            Get.snackbar(
                              'Success',
                              'Service request submitted successfully',
                              snackPosition: SnackPosition.BOTTOM,
                            );
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.blue.shade700,
                            foregroundColor: Colors.white,
                            elevation: 0,
                            padding: const EdgeInsets.symmetric(
                              horizontal: 22,
                              vertical: 14,
                            ),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                          ),
                          child: const Text(
                            'Submit Request',
                            style: TextStyle(fontWeight: FontWeight.bold),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            );
          },
        ),
      ),
      barrierDismissible: true,
    );
  }

  Widget _fieldLabel(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Text(
        text,
        style: TextStyle(
          fontSize: 12,
          fontWeight: FontWeight.w800,
          color: Colors.blueGrey.shade600,
          letterSpacing: 0.4,
        ),
      ),
    );
  }

  InputDecoration _inputDecoration({String? hint}) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(
        color: Color(0xff94A3B8),
        fontSize: 14,
      ),
      filled: true,
      fillColor: Colors.white,
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide(color: Colors.grey.shade300),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide(color: Colors.blue.shade700, width: 1.4),
      ),
    );
  }

  bool _isCurrentUserLoggedIn() {
    /// Replace this with your real auth logic.
    return false;
  }

  void _handleMessageTap(BuildContext context) {
    if (!_isCurrentUserLoggedIn()) {
      _showAuthenticationRequiredDialog(context);
      return;
    }

    _showMessageDialog(context);
  }

  void _showAuthenticationRequiredDialog(BuildContext context) {
    Get.dialog(
      Dialog(
        insetPadding: const EdgeInsets.symmetric(horizontal: 24),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
        ),
        child: Padding(
          padding: const EdgeInsets.fromLTRB(24, 24, 24, 22),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Align(
                alignment: Alignment.topRight,
                child: GestureDetector(
                  onTap: () => Get.back(),
                  child: const Icon(
                    Icons.close,
                    color: Color(0xff94A3B8),
                  ),
                ),
              ),

              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: Colors.orange.shade50,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(
                  Icons.lock_outline,
                  color: Colors.orange.shade700,
                  size: 24,
                ),
              ),

              const SizedBox(height: 22),

              const Text(
                'Authentication Required',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                  color: Color(0xff111827),
                ),
              ),

              const SizedBox(height: 12),

              const Text(
                'Please log in or register a new customer\naccount to send messages or submit requests\nto providers.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 14,
                  color: Color(0xff64748B),
                  height: 1.45,
                  fontWeight: FontWeight.w500,
                ),
              ),

              const SizedBox(height: 26),

              SizedBox(
                width: double.infinity,
                height: 44,
                child: ElevatedButton(
                  onPressed: () {
                    Get.back();
                    Get.to(() => const LoginScreenView());
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.blue.shade700,
                    foregroundColor: Colors.white,
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                  child: const Text(
                    'Log In',
                    style: TextStyle(
                      fontWeight: FontWeight.w800,
                      fontSize: 14,
                    ),
                  ),
                ),
              ),

              const SizedBox(height: 10),

              SizedBox(
                width: double.infinity,
                height: 44,
                child: OutlinedButton(
                  onPressed: () {
                    Get.back();
                    Get.to(() => const SignupScreenView());
                  },
                  style: OutlinedButton.styleFrom(
                    foregroundColor: const Color(0xff334155),
                    side: BorderSide(color: Colors.grey.shade300),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                  child: const Text(
                    'Create Account',
                    style: TextStyle(
                      fontWeight: FontWeight.w800,
                      fontSize: 14,
                    ),
                  ),
                ),
              ),

              const SizedBox(height: 16),

              GestureDetector(
                onTap: () => Get.back(),
                child: const Text(
                  'Continue Browsing',
                  style: TextStyle(
                    color: Color(0xff94A3B8),
                    fontSize: 13,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
      barrierDismissible: true,
    );
  }

  void _showMessageDialog(BuildContext context) {
    final TextEditingController messageController = TextEditingController();

    Get.dialog(
      Dialog(
        insetPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 24),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
        ),
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: SizedBox(
            width: double.infinity,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                /// Header
                Row(
                  children: [
                    Container(
                      width: 42,
                      height: 42,
                      decoration: BoxDecoration(
                        color: Colors.blue.shade50,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Icon(
                        Icons.chat_bubble_outline,
                        color: Colors.blue.shade700,
                        size: 21,
                      ),
                    ),
                    const SizedBox(width: 12),
                    const Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Message Deepak Sharma',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w800,
                              color: Color(0xff111827),
                            ),
                          ),
                          SizedBox(height: 2),
                          Text(
                            'Live Chat History',
                            style: TextStyle(
                              fontSize: 13,
                              color: Color(0xff64748B),
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ],
                      ),
                    ),
                    GestureDetector(
                      onTap: () => Get.back(),
                      child: const Icon(
                        Icons.close,
                        color: Color(0xff94A3B8),
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 18),

                /// Chat history box
                Container(
                  height: 300,
                  width: double.infinity,
                  decoration: BoxDecoration(
                    color: const Color(0xffF8FAFC),
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: const Color(0xffE2E8F0)),
                  ),
                  child: const Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(
                        Icons.chat_bubble,
                        size: 42,
                        color: Color(0xffC4B5FD),
                      ),
                      SizedBox(height: 12),
                      Text(
                        'No prior conversation found.',
                        style: TextStyle(
                          fontSize: 13,
                          color: Color(0xff94A3B8),
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      SizedBox(height: 4),
                      Text(
                        'Type below to start a secure conversation thread.',
                        style: TextStyle(
                          fontSize: 11,
                          color: Color(0xff94A3B8),
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 16),

                /// Message input
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: const Color(0xffE2E8F0)),
                  ),
                  child: Row(
                    children: [
                      Expanded(
                        child: TextField(
                          controller: messageController,
                          minLines: 1,
                          maxLines: 3,
                          decoration: const InputDecoration(
                            border: InputBorder.none,
                            hintText: 'Type your message here...',
                            hintStyle: TextStyle(
                              color: Color(0xff94A3B8),
                              fontSize: 14,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      GestureDetector(
                        onTap: () {
                          if (messageController.text.trim().isEmpty) {
                            return;
                          }

                          /// Send message API call here.
                          final message = messageController.text.trim();

                          messageController.clear();

                          Get.snackbar(
                            'Message Sent',
                            message,
                            snackPosition: SnackPosition.BOTTOM,
                          );
                        },
                        child: Container(
                          width: 42,
                          height: 42,
                          decoration: BoxDecoration(
                            color: Colors.blue.shade300,
                            borderRadius: BorderRadius.circular(14),
                          ),
                          child: const Icon(
                            Icons.send_outlined,
                            color: Colors.white,
                            size: 22,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
      barrierDismissible: true,
    );
  }

  Widget _buildBottomNav() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, -5),
          )
        ],
      ),
      child: SafeArea(
        child: Row(
          children: [
            Expanded(
              flex: 5,
              child: SizedBox(
                height: 48,
                child: ElevatedButton.icon(
                  onPressed: () => _showContactInfo(context),
                  icon: const Icon(Icons.call_outlined, color: Colors.white, size: 16),
                  label: const Text(
                    'Contact Info',
                    style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.blue.shade700,
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                ),
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              flex: 4,
              child: SizedBox(
                height: 48,
                child: OutlinedButton.icon(
                  onPressed: () {
                    if (!_isCurrentUserLoggedIn()) {
                      _showAuthenticationRequiredDialog(context);
                      return;
                    }
                    _showSubmitRequestDialog(context);
                  },
                  icon: const Icon(Icons.calendar_today_outlined, color: Colors.black87, size: 16),
                  label: const Text(
                    'Submit Request',
                    style: TextStyle(color: Colors.black87, fontWeight: FontWeight.bold, fontSize: 11),
                  ),
                  style: OutlinedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(horizontal: 2),
                    side: BorderSide(color: Colors.grey.shade300),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                ),
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              flex: 4,
              child: SizedBox(
                height: 48,
                child: OutlinedButton.icon(
                  onPressed: () => _handleMessageTap(context),
                  icon: const Icon(Icons.message_outlined, color: Colors.black87, size: 16),
                  label: const Text(
                    'Message',
                    style: TextStyle(color: Colors.black87, fontWeight: FontWeight.bold, fontSize: 11),
                  ),
                  style: OutlinedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(horizontal: 4),
                    side: BorderSide(color: Colors.grey.shade300),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
