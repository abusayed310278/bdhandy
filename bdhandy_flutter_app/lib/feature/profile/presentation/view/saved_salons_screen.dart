import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../../core/colors/app_color.dart';
import '../../../../core/storage/favorites_manager.dart';
import '../../../home/data/model/salon_model.dart';
import '../../../home/presentation/controller/home_controller.dart';

class SavedSalonsScreen extends StatelessWidget {
  const SavedSalonsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final FavoritesManager favoritesManager = Get.find<FavoritesManager>();

    return Scaffold(
      backgroundColor: AppColor.background,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        leading: GestureDetector(
          onTap: () => Get.back(),
          child: const Icon(
            Icons.arrow_back_ios_new,
            size: 18,
            color: AppColor.textPrimary,
          ),
        ),
        title: Text(
          'Saved Salons',
          style: GoogleFonts.poppins(
            fontSize: 18,
            fontWeight: FontWeight.w600,
            color: AppColor.textPrimary,
          ),
        ),
      ),
      body: Obx(() {
        final salons = favoritesManager.savedSalons;

        if (salons.isEmpty) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(
                  Icons.favorite_border,
                  size: 64,
                  color: AppColor.textSecondary.withOpacity(0.5),
                ),
                const SizedBox(height: 16),
                Text(
                  'No saved salons yet',
                  style: GoogleFonts.poppins(
                    fontSize: 16,
                    fontWeight: FontWeight.w500,
                    color: AppColor.textSecondary,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  'Explore and save your favorite salons!',
                  style: GoogleFonts.poppins(
                    fontSize: 14,
                    color: AppColor.textSecondary,
                  ),
                ),
              ],
            ),
          );
        }

        return ListView.builder(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
          itemCount: salons.length,
          itemBuilder: (context, index) {
            return _SalonCard(salon: salons[index]);
          },
        );
      }),
    );
  }
}

class _SalonCard extends StatelessWidget {
  final Salon salon;

  const _SalonCard({required this.salon});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      // onTap: () {
      //   Get.find<HomeController>().selectSalon(salon);
      //   Get.to(() => const SalonDetailsScreen());
      // },
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 10,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          children: [
            // Salon thumbnail
            Container(
              width: 64,
              height: 64,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(12),
                gradient: salon.imageUrl == null
                    ? LinearGradient(
                        colors: [
                          Color(int.parse('FF${salon.imageColor}', radix: 16)),
                          Color(
                            int.parse('FF${salon.imageColor}', radix: 16),
                          ).withOpacity(0.55),
                        ],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      )
                    : null,
                image: salon.imageUrl != null
                    ? DecorationImage(
                        image: CachedNetworkImageProvider(salon.imageUrl!),
                        fit: BoxFit.cover,
                      )
                    : null,
              ),
              child: salon.imageUrl == null
                  ? Center(
                      child: Text(
                        salon.name.isNotEmpty ? salon.name[0] : 'S',
                        style: GoogleFonts.poppins(
                          color: Colors.white,
                          fontSize: 24,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    )
                  : null,
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    salon.name,
                    style: GoogleFonts.poppins(
                      fontSize: 15,
                      fontWeight: FontWeight.w600,
                      color: AppColor.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 3),
                  Row(
                    children: [
                      const Icon(
                        Icons.star,
                        color: Color(0xFFFFC107),
                        size: 14,
                      ),
                      const SizedBox(width: 3),
                      Text(
                        '${salon.rating} (${salon.reviewCount})',
                        style: GoogleFonts.poppins(
                          fontSize: 12,
                          color: AppColor.textSecondary,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 3),
                  Text(
                    salon.address,
                    style: GoogleFonts.poppins(
                      fontSize: 12,
                      color: AppColor.textSecondary,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text(
                  '${salon.distanceKm} km',
                  style: GoogleFonts.poppins(
                    fontSize: 12,
                    color: AppColor.textSecondary,
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(height: 6),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 3,
                  ),
                  decoration: BoxDecoration(
                    color: salon.isOpen
                        ? AppColor.success.withOpacity(0.1)
                        : AppColor.error.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    salon.isOpen ? 'Open' : 'Closed',
                    style: GoogleFonts.poppins(
                      fontSize: 10,
                      fontWeight: FontWeight.w600,
                      color: salon.isOpen ? AppColor.success : AppColor.error,
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
