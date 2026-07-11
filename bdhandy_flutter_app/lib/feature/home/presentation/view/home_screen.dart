import 'package:bdhandy_flutter_app/feature/provider/presentation/view/provider_profile_screen.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../controller/home_controller.dart';
import 'notification_screen.dart';
import 'package:bdhandy_flutter_app/feature/provider/presentation/view/all_providers_screen.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      body: SafeArea(
        top: false,
        child: Column(
          children: [
            _buildHeader(),
            Expanded(
              child: SingleChildScrollView(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 20),
                    _buildPopularServices(),
                    const SizedBox(height: 20),
                    _buildProviderList("Top Providers"),
                    const SizedBox(height: 20),
                    _buildProviderList("Featured Providers"),
                    const SizedBox(height: 20),
                    _buildProviderList("Top Rated"),
                    const SizedBox(height: 20),
                    _buildProviderList("Freelancer Providers"),
                    const SizedBox(height: 20),
                    _buildProviderList("Business Providers"),
                    const SizedBox(height: 20),
                    _buildTestimonials(),
                    const SizedBox(height: 24),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      padding: const EdgeInsets.only(top: 50, left: 24, right: 24, bottom: 24),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xff16B83E), Color(0xff0F8E2E)],
        ),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(30),
          bottomRight: Radius.circular(30),
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: const [
                  CircleAvatar(
                    radius: 24,
                    backgroundColor: Colors.white24,
                    child: Icon(Icons.person, color: Colors.white),
                  ),
                  SizedBox(width: 12),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Hi, User 👋',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      SizedBox(height: 4),
                      Text(
                        'What service do you need?',
                        style: TextStyle(
                          color: Colors.white70,
                          fontSize: 13,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              GestureDetector(
                onTap: () => Get.to(() => const NotificationScreen()),
                child: Stack(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: const BoxDecoration(
                        color: Colors.white24,
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(
                        Icons.notifications_none,
                        color: Colors.white,
                        size: 24,
                      ),
                    ),
                    Obx(() {
                      final count = Get.find<HomeController>()
                          .unreadNotificationsCount
                          .value;
                      if (count == 0) return const SizedBox.shrink();
                      return Positioned(
                        right: 2,
                        top: 2,
                        child: Container(
                          height: 10,
                          width: 10,
                          decoration: const BoxDecoration(
                            color: Colors.redAccent,
                            shape: BoxShape.circle,
                          ),
                        ),
                      );
                    }),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          GestureDetector(
            onTap: () {
              final controller = Get.find<HomeController>();
              controller.currentIndex.value = 1;
            },
            child: Container(
              height: 54,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.08),
                    blurRadius: 15,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Row(
                children: [
                  const Icon(Icons.search, color: Colors.grey, size: 22),
                  const SizedBox(width: 12),
                  Text(
                    'Search for a service...',
                    style: TextStyle(color: Colors.grey.shade400, fontSize: 14),
                  ),
                  const Spacer(),
                  Container(
                    padding: const EdgeInsets.all(6),
                    decoration: BoxDecoration(
                      color: const Color(0xffF4FBF5),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: const Icon(
                      Icons.tune,
                      color: Color(0xff16B83E),
                      size: 20,
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

  Widget _buildPopularServices() {
    final services = [
      {
        'icon': Icons.ac_unit,
        'name': 'AC Repair',
        'bg': const Color(0xffE2F6E9),
        'iconColor': const Color(0xff16B83E),
      },
      {
        'icon': Icons.electrical_services,
        'name': 'Electrician',
        'bg': const Color(0xffFFF7E6),
        'iconColor': const Color(0xffFFAA00),
      },
      {
        'icon': Icons.cleaning_services,
        'name': 'Cleaning',
        'bg': const Color(0xffF3E8FF),
        'iconColor': const Color(0xff9B51E0),
      },
      {
        'icon': Icons.plumbing,
        'name': 'Plumbing',
        'bg': const Color(0xffE8F0FE),
        'iconColor': const Color(0xff4285F4),
      },
      {
        'icon': Icons.format_paint,
        'name': 'Painting',
        'bg': const Color(0xffFFEFE5),
        'iconColor': const Color(0xffFF7A00),
      },
      {
        'icon': Icons.local_shipping,
        'name': 'Moving',
        'bg': const Color(0xffE0F7FA),
        'iconColor': const Color(0xff00BCD4),
      },
      {
        'icon': Icons.handyman,
        'name': 'Carpentry',
        'bg': const Color(0xffFFF3E0),
        'iconColor': const Color(0xffFF9800),
      },
      {
        'icon': Icons.more_horiz,
        'name': 'More',
        'bg': const Color(0xffF5F5F5),
        'iconColor': Colors.black54,
      },
    ];

    return Container(
      color: Colors.white,
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 24),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Popular Services',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                  color: Color(0xff111827),
                ),
              ),
              Text(
                'View All',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: const Color(0xff16B83E),
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          GridView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: services.length,
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 4,
              mainAxisSpacing: 24,
              crossAxisSpacing: 16,
              childAspectRatio: 0.75,
            ),
            itemBuilder: (context, index) {
              return Column(
                children: [
                  Container(
                    height: 58,
                    width: 58,
                    decoration: BoxDecoration(
                      color: services[index]['bg'] as Color,
                      borderRadius: BorderRadius.circular(18),
                      boxShadow: [
                        BoxShadow(
                          color: (services[index]['iconColor'] as Color)
                              .withOpacity(0.15),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Icon(
                      services[index]['icon'] as IconData,
                      color: services[index]['iconColor'] as Color,
                      size: 28,
                    ),
                  ),
                  const SizedBox(height: 10),
                  Text(
                    services[index]['name'] as String,
                    style: TextStyle(
                      fontSize: 11,
                      color: Colors.grey.shade800,
                      fontWeight: FontWeight.w600,
                    ),
                    textAlign: TextAlign.center,
                  ),
                ],
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildProviderList(String title) {
    final providers = [
      {
        "name": "Sparkle Clean",
        "service": "Home Cleaning",
        "rating": "4.8",
        "image": Colors.blueGrey,
      },
      {
        "name": "FixRight",
        "service": "Plumbing",
        "rating": "4.7",
        "image": Colors.orange,
      },
      {
        "name": "QuickFix",
        "service": "Appliance Repair",
        "rating": "4.9",
        "image": Colors.grey,
      },
      {
        "name": "Pro Electric",
        "service": "Electrical",
        "rating": "4.8",
        "image": Colors.green,
      },
    ];

    return Container(
      color: Colors.white,
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                  color: Color(0xff111827),
                ),
              ),
              GestureDetector(
                onTap: () => Get.to(() => AllProvidersScreen(title: title)),
                child: const Text(
                  "See all",
                  style: TextStyle(
                    color: Color(0xff16B83E),
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 18),
          SizedBox(
            height: 195,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              clipBehavior: Clip.none,
              itemCount: providers.length,
              itemBuilder: (context, index) {
                return Padding(
                  padding: const EdgeInsets.only(right: 16),
                  child: GestureDetector(
                    onTap: () => Get.to(() => const ProviderProfileScreen()),
                    child: _smallProviderCard(
                      name: providers[index]["name"] as String,
                      service: providers[index]["service"] as String,
                      rating: providers[index]["rating"] as String,
                      imageColor: providers[index]["image"] as Color,
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _smallProviderCard({
    required String name,
    required String service,
    required String rating,
    required Color imageColor,
  }) {
    return Container(
      width: 130,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 15,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          ClipRRect(
            borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
            child: Container(
              height: 100,
              width: double.infinity,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [imageColor.withOpacity(0.8), imageColor],
                ),
              ),
              child: const Icon(Icons.person, size: 45, color: Colors.white),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  name,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: Color(0xff111827),
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  service,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(fontSize: 11, color: Colors.grey.shade600),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    const Icon(
                      Icons.star_rounded,
                      color: Colors.amber,
                      size: 16,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      rating,
                      style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: Color(0xff111827),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTestimonials() {
    final reviews = [
      {
        "name": "Sadia M.",
        "location": "Dhanmondi, Dhaka",
        "review":
            "\"Found an electrician within 10 minutes. He came the same day and fixed everything. PICKHANDY is now my first stop for any home repair.\"",
        "initials": "SM",
        "color": Colors.blue.shade50,
        "textColor": Colors.blue.shade700,
      },
      {
        "name": "Karim H.",
        "location": "Banani, Dhaka",
        "review":
            "\"I run a small office and we use PICKHANDY for AC servicing, cleaning, and plumbing. Verified providers, fair prices, no haggling.\"",
        "initials": "KH",
        "color": Colors.orange.shade50,
        "textColor": Colors.orange.shade700,
      },
      {
        "name": "Nadia J.",
        "location": "Bashundhara, Dhaka",
        "review":
            "\"Posted a requirement at midnight, had 4 proposals by morning. Hired one, work done by evening. Couldn't be easier.\"",
        "initials": "NJ",
        "color": Colors.blue.shade50,
        "textColor": Colors.blue.shade700,
      },
    ];

    return Container(
      color: Colors.white,
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 28),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            "What our customers say",
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.w800,
              color: Color(0xff111827),
            ),
          ),
          const SizedBox(height: 20),
          SizedBox(
            height: 220,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              clipBehavior: Clip.none,
              itemCount: reviews.length,
              itemBuilder: (context, index) {
                final review = reviews[index];
                return Padding(
                  padding: const EdgeInsets.only(right: 16),
                  child: Container(
                    width: 300,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.04),
                          blurRadius: 15,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: List.generate(
                            5,
                            (index) => const Icon(
                              Icons.star_rounded,
                              color: Colors.amber,
                              size: 18,
                            ),
                          ),
                        ),
                        const SizedBox(height: 16),
                        Expanded(
                          child: Text(
                            review["review"] as String,
                            style: TextStyle(
                              fontSize: 13,
                              color: Colors.grey.shade700,
                              height: 1.5,
                              fontStyle: FontStyle.italic,
                            ),
                            maxLines: 4,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        const SizedBox(height: 12),
                        Divider(color: Colors.grey.shade100),
                        const SizedBox(height: 12),
                        Row(
                          children: [
                            CircleAvatar(
                              radius: 20,
                              backgroundColor: review["color"] as Color,
                              child: Text(
                                review["initials"] as String,
                                style: TextStyle(
                                  color: review["textColor"] as Color,
                                  fontSize: 13,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  review["name"] as String,
                                  style: const TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.black87,
                                  ),
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  review["location"] as String,
                                  style: TextStyle(
                                    fontSize: 11,
                                    color: Colors.grey.shade500,
                                  ),
                                ),
                              ],
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
        ],
      ),
    );
  }
}
