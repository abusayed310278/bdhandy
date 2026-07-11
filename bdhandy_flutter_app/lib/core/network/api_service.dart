import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:dio/dio.dart';
import 'package:dio/io.dart';

import 'session_manager.dart';
import 'api_endpoints.dart';

class ApiService {
  static const String baseUrlStr = 'https://bdhandy.test/api/';
  static String get mediaBaseUrl => baseUrlStr.replaceAll('api/', '');

  late Dio _dio;

  ApiService() {
    _dio = Dio(
      BaseOptions(
        baseUrl: baseUrlStr,
        connectTimeout: const Duration(seconds: 30),
        receiveTimeout: const Duration(seconds: 30),
        headers: {'Accept': 'application/json', 'Connection': 'close'},
      ),
    );

    // Bypass SSL Verification for local Laragon HTTPS redirects
    _dio.httpClientAdapter = IOHttpClientAdapter(
      createHttpClient: () {
        final client = HttpClient();
        client.badCertificateCallback =
            (X509Certificate cert, String host, int port) => true;
        return client;
      },
    );

    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) {
          final token = SessionManager.getToken();
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          
          print('--> API REQUEST: ${options.method} ${options.uri}');
          print('Request Body: ${options.data}');
          
          // Invisible Emulator DNS Bypass: 
          // Android emulator cannot resolve .test domains, so we route the connection
          // directly to the host machine (10.0.2.2) while preserving the bdhandy.test Host header
          // so Laragon routes it correctly.
          if (options.uri.host == 'bdhandy.test') {
            options.headers['Host'] = 'bdhandy.test';
            options.path = options.uri.toString().replaceFirst('bdhandy.test', '10.0.2.2');
          }
          
          return handler.next(options);
        },
        onResponse: (response, handler) {
          print(
            '<-- API RESPONSE: [Status ${response.statusCode}] for ${response.requestOptions.uri}',
          );
          try {
            final prettyJson = const JsonEncoder.withIndent(
              '  ',
            ).convert(response.data);
            print('Response Body:\n$prettyJson');
          } catch (e) {
            print('Response Body: ${response.data}');
          }
          return handler.next(response);
        },
        onError: (DioException e, handler) {
          print(
            '<-- API ERROR: [Status ${e.response?.statusCode}] for ${e.requestOptions.uri}',
          );
          print('Error Type: ${e.type}');
          print('Error Message: ${e.message}');
          try {
            final prettyJson = const JsonEncoder.withIndent(
              '  ',
            ).convert(e.response?.data);
            print('Response Body:\n$prettyJson');
          } catch (_) {
            print('Response Body: ${e.response?.data}');
          }
          return handler.next(e);
        },
      ),
    );
  }

  void setToken(String? token) {}

  Future<Response> _mockResponse(dynamic data, {int statusCode = 200}) async {
    await Future.delayed(const Duration(milliseconds: 600));
    return Response(
      requestOptions: RequestOptions(path: ''),
      statusCode: statusCode,
      data: data,
    );
  }

  Future<Response> login(String email, String password) {
    return _dio.post(
      ApiEndpoints.login,
      data: {'email': email, 'password': password},
    );
  }

  Future<Response> register({
    required String name,
    required String email,
    required String password,
    String? phoneCountryCode,
    String? phone,
    String? dob,
    String? gender,
  }) {
    return _dio.post(ApiEndpoints.register, data: {
      'name': name,
      'email': email,
      'password': password,
      'password_confirmation': password,
      if (phoneCountryCode != null && phoneCountryCode.isNotEmpty) 'phone_country_code': phoneCountryCode,
      if (phone != null && phone.isNotEmpty) 'phone': phone,
      if (dob != null && dob.isNotEmpty) 'dob': dob,
      if (gender != null && gender.isNotEmpty) 'gender': gender.toLowerCase(),
      'role': 'customer', // Assuming app is mostly for customers initially
    });
  }

  Future<Response> sendResetOtp(String email) {
    return _dio.post(ApiEndpoints.passwordEmail, data: {'email': email});
  }

  Future<Response> resetPassword({
    required String email,
    required String otp,
    required String password,
  }) {
    return _dio.post(
      ApiEndpoints.passwordReset,
      data: {
        'email': email,
        'token': otp,
        'password': password,
        'password_confirmation': password,
      },
    );
  }

  Future<Response> getHowItWorks() => _mockResponse({
    'success': true,
    'data': '<h2>How It Works</h2><p>Mock content</p>',
  });
  Future<Response> getFeatures() => _mockResponse({
    'success': true,
    'data': '<h2>Features</h2><p>Mock content</p>',
  });
  Future<Response> getAboutUs() => _mockResponse({
    'success': true,
    'data': '<h2>About Us</h2><p>Mock content</p>',
  });
  Future<Response> getContactInfo() => _mockResponse({
    'success': true,
    'data': '<h2>Contact</h2><p>Mock content</p>',
  });
  Future<Response> getPrivacyPolicy() => _mockResponse({
    'success': true,
    'data': '<h2>Privacy Policy</h2><p>Mock content</p>',
  });
  Future<Response> getTermsConditions() => _mockResponse({
    'success': true,
    'data': '<h2>Terms</h2><p>Mock content</p>',
  });

  Future<Response> changePassword({
    required String currentPassword,
    required String newPassword,
    required String newPasswordConfirmation,
  }) {
    return _dio.post(
      ApiEndpoints.changePassword,
      data: {
        'current_password': currentPassword,
        'password': newPassword,
        'password_confirmation': newPasswordConfirmation,
      },
    );
  }

  Future<Response> getTickets() => _mockResponse({'success': true, 'data': []});
  Future<Response> createTicket({
    required String subject,
    required String details,
    String? attachmentPath,
  }) => _mockResponse({'success': true});
  Future<Response> submitContact({
    required String name,
    required String email,
    required String subject,
    required String message,
  }) => _mockResponse({'success': true});

  Future<Response> getCountries() =>
      _mockResponse({'success': true, 'data': []});
  Future<Response> getProfile() => _mockResponse({
    'success': true,
    'data': {'name': 'Demo User', 'email': 'demo@example.com'},
  });
  Future<Response> getStates(int countryId) =>
      _mockResponse({'success': true, 'data': []});
  Future<Response> getCities(int stateId) =>
      _mockResponse({'success': true, 'data': []});

  Future<Response> updateProfile({
    required String name,
    String? contact,
    String? dob,
    String? gender,
    String? address,
    int? countryId,
    int? stateId,
    int? cityId,
    String? photoPath,
  }) => _mockResponse({
    'success': true,
    'data': {
      'user': {
        'name': name,
        'contact': contact,
        'dob': dob,
        'gender': gender,
        'address': address,
        'photo': photoPath,
      },
    },
  });

  final _dummyBranches = [
    {
      'id': 1,
      'name': 'Glow & Glamour Salon',
      'address': '123 Beauty Ave',
      'city': {'name': 'New York'},
      'ratings_avg_rating': '4.8',
      'ratings_count': '120',
      'status': 1,
      'photo':
          'https://images.unsplash.com/photo-1521590832167-7bfc17484d20?w=500&q=80',
      'banner_photo':
          'https://images.unsplash.com/photo-1521590832167-7bfc17484d20?w=500&q=80',
      'about': 'Premium salon offering the best styling and grooming services.',
      'listing': {
        'services': [
          {'name': 'Haircut'},
          {'name': 'Coloring'},
          {'name': 'Styling'},
        ],
      },
    },
    {
      'id': 2,
      'name': 'Urban Trim Barbershop',
      'address': '456 Downtown Blvd',
      'city': {'name': 'Chicago'},
      'ratings_avg_rating': '4.9',
      'ratings_count': '340',
      'status': 1,
      'photo':
          'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=500&q=80',
      'banner_photo':
          'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=500&q=80',
      'about': 'Classic cuts and modern styles for the urban gentleman.',
      'listing': {
        'services': [
          {'name': 'Fade'},
          {'name': 'Beard Trim'},
          {'name': 'Shave'},
        ],
      },
    },
    {
      'id': 3,
      'name': 'Serenity Spa & Wellness',
      'address': '789 Quiet Lane',
      'city': {'name': 'Los Angeles'},
      'ratings_avg_rating': '4.7',
      'ratings_count': '89',
      'status': 1,
      'photo':
          'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=500&q=80',
      'banner_photo':
          'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=500&q=80',
      'about': 'Relax, rejuvenate, and refresh your mind and body.',
      'listing': {
        'services': [
          {'name': 'Massage'},
          {'name': 'Facial'},
          {'name': 'Manicure'},
        ],
      },
    },
  ];

  Future<Response> getHomeData() {
    return _mockResponse({
      'success': true,
      'data': {
        'featured_branches': [_dummyBranches[0], _dummyBranches[1]],
        'new_branches': [_dummyBranches[2]],
        'popular_branches': _dummyBranches,
        'top_reviews': [
          {
            'user': {'name': 'Alice Johnson'},
            'comment': 'Absolutely loved my haircut here. Highly recommended!',
            'rating': 5,
          },
          {
            'user': {'name': 'Michael Smith'},
            'comment': 'Great vibe, professional staff.',
            'rating': 4.5,
          },
        ],
        'categories': [
          {
            'id': 1,
            'name': 'Haircut',
            'icon': 'https://cdn-icons-png.flaticon.com/512/3011/3011036.png',
          },
          {
            'id': 2,
            'name': 'Makeup',
            'icon': 'https://cdn-icons-png.flaticon.com/512/1940/1940986.png',
          },
          {
            'id': 3,
            'name': 'Massage',
            'icon': 'https://cdn-icons-png.flaticon.com/512/2950/2950993.png',
          },
          {
            'id': 4,
            'name': 'Nails',
            'icon': 'https://cdn-icons-png.flaticon.com/512/1940/1940947.png',
          },
        ],
      },
    });
  }

  Future<Response> getBranchDetails(String slug) {
    return _mockResponse({
      'success': true,
      'data': {
        'branch': _dummyBranches.first,
        'categories': [
          {
            'name': 'Hair Services',
            'services': [
              {
                'id': 1,
                'name': 'Classic Haircut',
                'price': '30.00',
                'duration': 30,
                'photo':
                    'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=200&q=80',
              },
              {
                'id': 2,
                'name': 'Hair Coloring',
                'price': '80.00',
                'duration': 90,
                'photo':
                    'https://images.unsplash.com/photo-1519699047748-de8e457a634e?w=200&q=80',
              },
            ],
          },
        ],
        'staffs': [
          {
            'id': 1,
            'name': 'Jane Doe',
            'designation': 'Senior Stylist',
            'photo':
                'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&q=80',
          },
          {
            'id': 2,
            'name': 'Mark Roe',
            'designation': 'Barber',
            'photo':
                'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&q=80',
          },
        ],
        'reviews': [
          {
            'user': {'name': 'Alice'},
            'comment': 'Great!',
            'rating': 5,
            'created_at': '2024-05-12',
          },
        ],
      },
    });
  }

  Future<Response> exploreSalons({
    String? search,
    String? city,
    String? category,
    String? rating,
    String? sort,
  }) {
    return _mockResponse({
      'success': true,
      'data': {
        'branches': {'data': _dummyBranches},
      },
    });
  }

  Future<Response> getBookings({
    String? status,
    String? from,
    String? to,
    String? q,
  }) {
    return _mockResponse({
      'success': true,
      'data': {
        'bookings': {'data': []},
      },
    });
  }

  Future<Response> getNotifications() {
    return _mockResponse({
      'success': true,
      'data': {
        'notifications': {
          'data': [
            {
              'title': 'Booking Confirmed',
              'message': 'Your booking for Glow & Glamour has been confirmed.',
              'is_read': 0,
              'created_at': '2 mins ago',
            },
          ],
        },
      },
    });
  }

  Future<Response> switchLanguage(String locale) =>
      _mockResponse({'success': true});
  Future<Response> getBookingChat(int bookingId) =>
      _mockResponse({'success': true, 'data': []});
  Future<Response> sendBookingChatMessage(
    int bookingId, {
    String? details,
    String? attachmentPath,
  }) => _mockResponse({'success': true});
  Future<Response> markChatRead(int bookingId) =>
      _mockResponse({'success': true});
}
