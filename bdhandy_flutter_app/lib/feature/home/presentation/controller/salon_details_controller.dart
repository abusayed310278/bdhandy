import 'package:get/get.dart';
import 'package:dio/dio.dart';
import '../../../../core/network/api_service.dart';
import '../../../../core/storage/favorites_manager.dart';
import '../../data/model/salon_model.dart';

class SalonDetailsController extends GetxController {
  final String slug;

  SalonDetailsController({required this.slug});

  final isLoading = true.obs;
  final errorMessage = ''.obs;

  final branchData = Rxn<Map<String, dynamic>>();

  final isFavorite = false.obs;
  final FavoritesManager favoritesManager = Get.find<FavoritesManager>();

  void toggleFavorite() {
    isFavorite.value = !isFavorite.value;

    // We need a Salon model to save. Let's create one from the branchData.
    if (branchData.value != null) {
      final b = branchData.value!;
      final parent = b['listing'] ?? {};
      final salon = Salon(
        id: b['id'].toString(),
        listingId: b['listing_id']?.toString() ?? '',
        slug: b['slug'] ?? '',
        name: b['name'] ?? '',
        address: b['address'] ?? '',
        city: parent['city'] ?? '',
        rating: (b['rating'] ?? 0).toDouble(),
        reviewCount: b['review_count'] ?? 0,
        distanceKm: (b['distance'] ?? 0).toDouble(),
        openTime: b['opening_time'] ?? '',
        closeTime: b['closing_time'] ?? '',
        isOpen: true,
        imageColor: '6C3EE8',
        tags: [],
        about: b['description'] ?? '',
        imageUrl: b['banner_photo'] != null
            ? 'http://10.0.2.2/bdhandy/public/${b['banner_photo']}'
            : 'http://10.0.2.2/bdhandy/public/assets/default_banner.png',
      );
      favoritesManager.toggleFavorite(salon);
    }
  }

  @override
  void onInit() {
    super.onInit();
    isFavorite.value = favoritesManager.isFavorite(slug);
    fetchBranchDetails();
  }

  Future<void> fetchBranchDetails() async {
    isLoading.value = true;
    errorMessage.value = '';

    try {
      final apiService = Get.find<ApiService>();
      final response = await apiService.getBranchDetails(slug);
      if (response.data != null && response.data['success'] == true) {
        branchData.value = response.data['data']['branch'];
      } else {
        errorMessage.value = 'Failed to load salon details.';
      }
    } on DioException catch (e) {
      errorMessage.value =
          'Dio Error: \${e.message} \${e.response?.statusCode}';
    } catch (e) {
      errorMessage.value = 'Error: $e';
    } finally {
      isLoading.value = false;
    }
  }

  // Mapping Helpers
  List<SalonService> get getServices {
    final rawServices = branchData.value?['listing']?['services'] ??
        branchData.value?['branch_services'] ??
        [];
    if (rawServices is! List) return [];

    return rawServices.map((s) {
      final serviceData = s['service'] ?? s;
      final photo = serviceData['photo']?.toString();
      return SalonService(
        id: serviceData['id']?.toString() ?? '',
        name: serviceData['name'] ?? 'Service',
        description: serviceData['details'] ?? serviceData['description'] ?? '',
        durationMins:
            double.tryParse(serviceData['required_time']?.toString() ?? '30')
                    ?.toInt() ??
                30,
        price: double.tryParse(serviceData['default_price']?.toString() ??
                    serviceData['price']?.toString() ??
                    '0')
                ?.toInt() ??
            0,
        category: serviceData['service_category']?['name'] ?? 'General',
        iconEmoji: '✨',
        imageUrl: photo != null && photo.isNotEmpty
            ? '${ApiService.mediaBaseUrl}$photo?v=1'
            : null,
      );
    }).toList();
  }

  List<SalonMaster> get getStaff {
    final rawStaff = branchData.value?['listing']?['staff'] ?? [];
    if (rawStaff is! List) return [];

    return rawStaff.map((st) {
      final user = st['user'] ?? {};
      final name = user['name'] ?? 'Staff';
      String initials = name.toString().substring(0, 1).toUpperCase();
      String? photo = user['photo']?.toString();

      return SalonMaster(
        id: st['id']?.toString() ?? '',
        name: name,
        role: st['designation'] ?? 'Professional',
        rating: 4.8,
        experience: '3+ years',
        avatarInitials: initials,
        avatarColor: 0xFF6C3EE8,
        photoUrl: photo != null && photo.isNotEmpty
            ? '${ApiService.mediaBaseUrl}$photo?v=1'
            : null,
      );
    }).toList();
  }

  List<String> get getGalleries {
    final rawGals = branchData.value?['galleries'] ??
        branchData.value?['listing']?['galleries'] ??
        [];
    if (rawGals is! List) return [];

    return rawGals
        .map((g) {
          final img = g['image']?.toString() ?? '';
          return img.isNotEmpty ? '${ApiService.mediaBaseUrl}$img?v=1' : '';
        })
        .where((s) => s.isNotEmpty)
        .toList();
  }

  String get getContact {
    return branchData.value?['contact'] ??
        branchData.value?['listing']?['contact'] ??
        '';
  }

  String get getAddress {
    return branchData.value?['address'] ??
        branchData.value?['listing']?['address'] ??
        '';
  }

  List<Map<String, dynamic>> get getSocialAccounts {
    final accounts = branchData.value?['listing']?['social_accounts'] ?? [];
    if (accounts is! List) return [];

    return accounts.map((acc) {
      String url = acc['url'] ?? '';
      String type = 'website';
      if (url.contains('facebook')) type = 'facebook';
      if (url.contains('instagram')) type = 'instagram';
      if (url.contains('youtube')) type = 'youtube';
      if (url.contains('twitter')) type = 'twitter';
      return {
        'url': url,
        'type': type,
      };
    }).toList();
  }

  String get getWorkingDays {
    try {
      final weekends = branchData.value?['weekends'] as List?;
      if (weekends == null || weekends.isEmpty) return 'Monday - Sunday';

      final closedDays = weekends
          .map((e) => e['name']?.toString().toLowerCase() ?? '')
          .toList();
      if (closedDays.contains('saturday') && closedDays.contains('sunday')) {
        return 'Monday - Friday';
      } else if (closedDays.contains('sunday')) {
        return 'Monday - Saturday';
      }
      return 'Monday - Sunday'; // fallback, can be more complex if needed
    } catch (_) {
      return 'Monday - Sunday';
    }
  }

  String get getWorkingHours {
    try {
      final branchServices = branchData.value?['branch_services'] as List?;
      if (branchServices == null || branchServices.isEmpty)
        return '09:00 - 21:00';

      String? minTime;
      String? maxTime;

      for (var bs in branchServices) {
        final timeSlots = bs['time_slots'] as List?;
        if (timeSlots != null) {
          for (var slot in timeSlots) {
            final start = slot['start_from']?.toString();
            final end = slot['start_to']?.toString();

            if (start != null &&
                (minTime == null || start.compareTo(minTime) < 0)) {
              minTime = start;
            }
            if (end != null &&
                (maxTime == null || end.compareTo(maxTime) > 0)) {
              maxTime = end;
            }
          }
        }
      }

      if (minTime != null && maxTime != null) {
        return '${_formatTime24(minTime)} - ${_formatTime24(maxTime)}';
      }
    } catch (_) {}
    return '09:00 - 21:00';
  }

  List<Map<String, dynamic>> get getDailyWorkingHours {
    final days = [
      'Monday',
      'Tuesday',
      'Wednesday',
      'Thursday',
      'Friday',
      'Saturday',
      'Sunday'
    ];
    final shortDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    final weekends = branchData.value?['weekends'] as List? ?? [];
    final closedDays =
        weekends.map((e) => e['name']?.toString().toLowerCase() ?? '').toList();

    final workingHoursStr = getWorkingHours;
    final currentWeekday = DateTime.now().weekday; // 1 = Monday, 7 = Sunday

    List<Map<String, dynamic>> dailyHours = [];

    for (int i = 0; i < 7; i++) {
      final isClosed = closedDays.contains(days[i].toLowerCase());
      final isToday = (i + 1) == currentWeekday;

      dailyHours.add({
        'day': shortDays[i],
        'isToday': isToday,
        'isClosed': isClosed,
        'hours': isClosed ? 'Closed' : workingHoursStr,
      });
    }

    return dailyHours;
  }

  String _formatTime(String time) {
    try {
      final parts = time.split(':');
      if (parts.length >= 2) {
        int hour = int.parse(parts[0]);
        final int min = int.parse(parts[1]);
        final String ampm = hour >= 12 ? 'PM' : 'AM';
        if (hour > 12) hour -= 12;
        if (hour == 0) hour = 12;
        return '$hour:${min.toString().padLeft(2, '0')} $ampm';
      }
    } catch (_) {}
    return time;
  }

  String _formatTime24(String time) {
    try {
      final parts = time.split(':');
      if (parts.length >= 2) {
        return '${parts[0].padLeft(2, '0')}:${parts[1].padLeft(2, '0')}';
      }
    } catch (_) {}
    return time;
  }
}
