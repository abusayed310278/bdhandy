import 'package:get/get.dart';
import 'package:dio/dio.dart';
import '../../data/model/salon_model.dart';
import '../../../../core/network/session_manager.dart';
import '../../../../core/network/api_service.dart';

class HomeController extends GetxController {
  // Observable User Data for instant updates across screens
  final userData = Rxn<Map<String, dynamic>>();

  // Reactive data lists loaded from API
  final featuredSalons = <Salon>[].obs;
  final newSalons = <Salon>[].obs;
  final popularSalons = <Salon>[].obs;
  final customerReviews = <Map<String, dynamic>>[].obs;
  final apiCategories = <Map<String, dynamic>>[].obs;
  final nearbySalons = <Salon>[].obs;
  final unreadNotificationsCount = 0.obs;

  final searchResults = <Salon>[].obs;
  final isSearching = false.obs;

  final isLoadingHome = true.obs;

  @override
  void onInit() {
    super.onInit();
    userData.value = SessionManager.getUserData();
    fetchHomeData();
  }

  void updateUserData(Map<String, dynamic>? data) {
    userData.value = data;
  }

  void fetchHomeData() async {
    isLoadingHome.value = true;
    final apiService = Get.find<ApiService>();

    // 1. Load Home endpoints
    try {
      final res = await apiService.getHomeData();
      if (res.statusCode == 200 && res.data != null) {
        final data = res.data;
        if (data['success'] == true) {
          final homeData = data['data'];

          // Map Featured
          final List<dynamic> featured = homeData['featured_branches'] ?? [];
          featuredSalons.assignAll(
            featured.map((b) => mapBranchToSalon(b)).toList(),
          );

          // Map New
          final List<dynamic> newB = homeData['new_branches'] ?? [];
          newSalons.assignAll(newB.map((b) => mapBranchToSalon(b)).toList());

          // Map Popular
          final List<dynamic> popular = homeData['popular_branches'] ?? [];
          popularSalons.assignAll(
            popular.map((b) => mapBranchToSalon(b)).toList(),
          );

          // Map Reviews
          final List<dynamic> reviews = homeData['top_reviews'] ?? [];
          customerReviews.assignAll(
            reviews
                .map(
                  (r) => {
                    'author': r['user']?['name'] ?? 'Client',
                    'review': r['comment'] ?? '',
                    'rating': double.tryParse(r['rating'].toString()) ?? 5.0,
                  },
                )
                .toList(),
          );

          // Map Categories
          final List<dynamic> cats = homeData['categories'] ?? [];
          apiCategories.assignAll(
            cats
                .map(
                  (c) => {
                    'id': c['id'],
                    'label': c['name'] ?? '',
                    'iconUrl': c['icon'] != null
                        ? '${ApiService.mediaBaseUrl}' + c['icon']
                        : '',
                  },
                )
                .toList(),
          );
        }
      }
    } catch (e) {
      print("Error fetching home data: $e");
    }

    // 2. Load "Top Salons Near You" (Explore query)
    try {
      final res = await apiService.exploreSalons();
      if (res.statusCode == 200 && res.data != null) {
        final data = res.data;
        if (data['success'] == true) {
          final List<dynamic> branches = data['data']['branches']['data'] ?? [];
          nearbySalons.assignAll(
            branches.map((b) => mapBranchToSalon(b)).toList(),
          );
        }
      }
    } catch (e) {
      print("Error fetching explore salons: $e");
    }

    // 3. Load notifications list for unread count
    try {
      final res = await apiService.getNotifications();
      if (res.statusCode == 200 && res.data != null) {
        final data = res.data;
        if (data['success'] == true) {
          final List<dynamic> notifs =
              data['data']['notifications']['data'] ?? [];
          final int count = notifs.where((n) => n['is_read'] == 0).length;
          unreadNotificationsCount.value = count;
        }
      }
    } catch (_) {}

    isLoadingHome.value = false;
  }

  Future<void> searchSalons({
    String? query,
    String? location,
    String? category,
    String? sort,
    String? rating,
  }) async {
    isSearching.value = true;
    final apiService = Get.find<ApiService>();
    try {
      // Map category label to category ID
      String? categoryId;
      if (category != null && category != 'All Categories') {
        final catObj = apiCategories.firstWhere(
          (c) => c['label'] == category,
          orElse: () => {},
        );
        categoryId = catObj['id']?.toString();
      }

      // Map sort string
      String? apiSort;
      if (sort == 'Top Rated')
        apiSort = 'rating';
      else if (sort == 'Newest')
        apiSort = 'newest';

      // Map rating
      String? apiRating = (rating != null && rating != 'Any Rating')
          ? rating
          : null;

      final res = await apiService.exploreSalons(
        search: query?.isNotEmpty == true ? query : null,
        city: location?.isNotEmpty == true ? location : null,
        category: categoryId,
        sort: apiSort,
        rating: apiRating,
      );

      if (res.statusCode == 200 && res.data != null) {
        final data = res.data;
        if (data['success'] == true) {
          final List<dynamic> branches = data['data']['branches']['data'] ?? [];
          searchResults.assignAll(
            branches.map((b) => mapBranchToSalon(b)).toList(),
          );
        }
      }
    } catch (e) {
      print("Error fetching search salons: $e");
    } finally {
      isSearching.value = false;
    }
  }

  Salon mapBranchToSalon(Map<String, dynamic> branch) {
    final id = branch['id']?.toString() ?? '';
    final name = branch['name'] ?? '';
    final address = branch['address'] ?? '';
    final city = branch['city']?['name'] ?? '';

    // Rating
    final double rating = branch['ratings_avg_rating'] != null
        ? double.tryParse(branch['ratings_avg_rating'].toString()) ?? 0.0
        : 0.0;

    // Review Count
    final int reviewCount = branch['ratings_count'] != null
        ? int.tryParse(branch['ratings_count'].toString()) ?? 0
        : 0;

    // Distance
    final double distanceKm = branch['distance'] != null
        ? double.tryParse(branch['distance'].toString()) ?? 0.0
        : 0.0;

    final openTime = branch['open_time'] ?? '9:00 AM';
    final closeTime = branch['close_time'] ?? '9:00 PM';

    // isOpen
    final bool isOpen = branch['status'] == 1;

    // tags
    final List<String> tags = [];
    if (branch['listing'] != null && branch['listing']['services'] != null) {
      for (var s in branch['listing']['services']) {
        if (s['name'] != null) {
          tags.add(s['name']);
        }
      }
    }
    if (tags.length > 3) tags.sublist(0, 3);
    if (tags.isEmpty) tags.addAll(['Hygiene', 'Top Rated']);

    final about =
        branch['about'] ??
        'Redefining style and wellness with professional services.';

    // Real image URL from branch photo or banner_photo
    String? imageUrl;
    final rawPhoto = branch['photo']?.toString() ?? '';
    final rawBanner = branch['banner_photo']?.toString() ?? '';
    final rawPhotoPath = (rawBanner.isNotEmpty && rawBanner != 'null')
        ? rawBanner
        : rawPhoto;
    if (rawPhotoPath.isNotEmpty) {
      imageUrl = '${ApiService.mediaBaseUrl}$rawPhotoPath?v=1';
    }

    // Image placeholder color mapping based on ID
    final colors = ['6C3EE8', 'E83E8C', '3EC8B4', 'FF9800'];
    final colorIndex = (int.tryParse(id) ?? 0) % colors.length;
    final imageColor = colors[colorIndex];

    return Salon(
      id: id,
      listingId: branch['listing_id']?.toString() ?? '1',
      slug: branch['slug']?.toString() ?? '',
      name: name,
      address: address,
      city: city,
      rating: rating,
      reviewCount: reviewCount,
      distanceKm: distanceKm,
      openTime: openTime,
      closeTime: closeTime,
      isOpen: isOpen,
      imageColor: imageColor,
      tags: tags,
      about: about,
      imageUrl: imageUrl,
    );
  }

  // Selected tab in bottom nav
  final currentIndex = 0.obs;

  // Mock data fallbacks for booking details
  final salons = MockData.salons.obs;
  final masters = MockData.masters.obs;
  final services = MockData.services.obs;
  final bookings = MockData.bookings.obs;

  // Booking flow state
  final selectedSalon = Rxn<Salon>();
  final selectedService = Rxn<SalonService>();
  final selectedMaster = Rxn<SalonMaster>();
  final selectedDate = Rx<DateTime>(DateTime(2024, 5, 20));
  final selectedTimeSlot = '11:00 AM'.obs;

  // Service filter tab
  final selectedServiceCategory = 'Hair'.obs;

  List<SalonService> get filteredServices => MockData.services
      .where((s) => s.category == selectedServiceCategory.value)
      .toList();

  List<Booking> bookingsByStatus(String status) =>
      MockData.bookings.where((b) => b.status == status).toList();

  void selectSalon(Salon salon) => selectedSalon.value = salon;
  void selectService(SalonService service) => selectedService.value = service;
  void selectMaster(SalonMaster master) => selectedMaster.value = master;
  void selectDate(DateTime date) => selectedDate.value = date;
  void selectTimeSlot(String slot) => selectedTimeSlot.value = slot;

  void resetBookingFlow() {
    selectedService.value = null;
    selectedMaster.value = null;
    selectedDate.value = DateTime(2024, 5, 20);
    selectedTimeSlot.value = '11:00 AM';
  }
}
