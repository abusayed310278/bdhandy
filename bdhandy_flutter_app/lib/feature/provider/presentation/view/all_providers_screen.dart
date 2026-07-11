import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'provider_profile_screen.dart';

class AllProvidersScreen extends StatelessWidget {
  final String title;

  const AllProvidersScreen({super.key, required this.title});

  @override
  Widget build(BuildContext context) {
    // Dummy data identical to what's in home screen
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
      {
        "name": "CleanHouse",
        "service": "Cleaning",
        "rating": "4.6",
        "image": Colors.blue,
      },
      {
        "name": "Top Painters",
        "service": "Painting",
        "rating": "4.9",
        "image": Colors.redAccent,
      },
    ];

    return Scaffold(
      backgroundColor: const Color(0xffF9FAFB),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, color: Color(0xff111827), size: 20),
          onPressed: () => Get.back(),
        ),
        title: Text(
          title,
          style: const TextStyle(
            color: Color(0xff111827),
            fontSize: 16,
            fontWeight: FontWeight.w800,
          ),
        ),
      ),
      body: GridView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: providers.length,
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          childAspectRatio: 0.75,
          crossAxisSpacing: 16,
          mainAxisSpacing: 16,
        ),
        itemBuilder: (context, index) {
          final provider = providers[index];
          return GestureDetector(
            onTap: () => Get.to(() => const ProviderProfileScreen()),
            child: _buildProviderCard(
              name: provider["name"] as String,
              service: provider["service"] as String,
              rating: provider["rating"] as String,
              imageColor: provider["image"] as Color,
            ),
          );
        },
      ),
    );
  }

  Widget _buildProviderCard({
    required String name,
    required String service,
    required String rating,
    required Color imageColor,
  }) {
    return Container(
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
          Expanded(
            child: Container(
              decoration: BoxDecoration(
                color: imageColor,
                borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
              ),
              child: Stack(
                children: [
                  Positioned(
                    top: 10,
                    right: 10,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.star, color: Colors.amber, size: 12),
                          const SizedBox(width: 4),
                          Text(
                            rating,
                            style: const TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ),
                  )
                ],
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  name,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w800,
                    color: Color(0xff111827),
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 4),
                Text(
                  service,
                  style: const TextStyle(
                    fontSize: 11,
                    color: Color(0xff64748B),
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
