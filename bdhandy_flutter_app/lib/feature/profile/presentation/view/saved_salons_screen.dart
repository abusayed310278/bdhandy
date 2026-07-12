import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../../core/colors/app_color.dart';
import '../../../../core/storage/favorites_manager.dart';
import '../../../home/data/model/salon_model.dart';
import '../../../home/presentation/controller/home_controller.dart';
import '../../../../core/network/api_service.dart';

class SavedSalonsScreen extends StatefulWidget {
  const SavedSalonsScreen({super.key});

  @override
  State<SavedSalonsScreen> createState() => _SavedSalonsScreenState();
}

class _SavedSalonsScreenState extends State<SavedSalonsScreen> {
  bool _isLoading = true;
  List<dynamic> _salons = [];

  @override
  void initState() {
    super.initState();
    _fetchSavedSalons();
  }

  Future<void> _fetchSavedSalons() async {
    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.getSavedProviders();
      if (response.statusCode == 200 && response.data['success'] == true) {
        setState(() {
          _salons = response.data['data'] ?? [];
        });
      }
    } catch (e) {
      debugPrint('Error fetching saved providers: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
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
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _salons.isEmpty
          ? Center(
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
          )
          : ListView.builder(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
          itemCount: _salons.length,
          itemBuilder: (context, index) {
            return _SalonCard(providerData: _salons[index]['provider'] ?? {});
          },
        ),
    );
  }
}

class _SalonCard extends StatelessWidget {
  final Map<String, dynamic> providerData;

  const _SalonCard({required this.providerData});

  @override
  Widget build(BuildContext context) {
    final String name = providerData['company_name'] ?? providerData['name'] ?? 'Unknown Provider';
    final String address = providerData['address'] ?? 'No Address';
    final String? imageUrl = providerData['logo'] != null 
        ? 'https://10.0.2.2${providerData['logo']}'
        : null;
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
                color: const Color(0xFFE2E8F0),
                image: imageUrl != null
                    ? DecorationImage(
                        image: CachedNetworkImageProvider(imageUrl),
                        fit: BoxFit.cover,
                      )
                    : null,
              ),
              child: imageUrl == null
                  ? Center(
                      child: Text(
                        name.isNotEmpty ? name[0] : 'P',
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
                    name,
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
                        '4.5 (10)',
                        style: GoogleFonts.poppins(
                          fontSize: 12,
                          color: AppColor.textSecondary,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 3),
                  Text(
                    address,
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
                  '1.2 km',
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
                    color: AppColor.success.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    'Open',
                    style: GoogleFonts.poppins(
                      fontSize: 10,
                      fontWeight: FontWeight.w600,
                      color: AppColor.success,
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
