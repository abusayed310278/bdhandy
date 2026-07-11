import 'dart:async';
import 'package:dio/dio.dart';

class ApiService {
  static const String baseUrlStr = 'https://dummy.api/';
  static String get mediaBaseUrl => '';

  ApiService();

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
    return _mockResponse({
      'success': true,
      'message': 'Login successful',
      'data': {
        'token': 'mock_token_123',
        'user': {
          'id': 1,
          'name': 'Demo User',
          'email': email,
          'contact': '1234567890',
        }
      }
    });
  }

  Future<Response> register({
    required String name,
    required String email,
    required String password,
    String? contact,
    String? dob,
    String? gender,
  }) {
    return _mockResponse({
      'success': true,
      'message': 'Registration successful',
      'data': {
        'token': 'mock_token_123',
        'user': {
          'id': 1,
          'name': name,
          'email': email,
        }
      }
    });
  }

  Future<Response> sendResetOtp(String email) => _mockResponse({'success': true});
  Future<Response> resetPassword({required String email, required String otp, required String password}) => _mockResponse({'success': true});
  
  Future<Response> getHowItWorks() => _mockResponse({'success': true, 'data': '<h2>How It Works</h2><p>Mock content</p>'});
  Future<Response> getFeatures() => _mockResponse({'success': true, 'data': '<h2>Features</h2><p>Mock content</p>'});
  Future<Response> getAboutUs() => _mockResponse({'success': true, 'data': '<h2>About Us</h2><p>Mock content</p>'});
  Future<Response> getContactInfo() => _mockResponse({'success': true, 'data': '<h2>Contact</h2><p>Mock content</p>'});
  Future<Response> getPrivacyPolicy() => _mockResponse({'success': true, 'data': '<h2>Privacy Policy</h2><p>Mock content</p>'});
  Future<Response> getTermsConditions() => _mockResponse({'success': true, 'data': '<h2>Terms</h2><p>Mock content</p>'});

  Future<Response> changePassword({required String currentPassword, required String newPassword, required String newPasswordConfirmation}) => _mockResponse({'success': true});

  Future<Response> getTickets() => _mockResponse({'success': true, 'data': []});
  Future<Response> createTicket({required String subject, required String details, String? attachmentPath}) => _mockResponse({'success': true});
  Future<Response> submitContact({required String name, required String email, required String subject, required String message}) => _mockResponse({'success': true});

  Future<Response> getCountries() => _mockResponse({'success': true, 'data': []});
  Future<Response> getProfile() => _mockResponse({'success': true, 'data': {'name': 'Demo User', 'email': 'demo@example.com'}});
  Future<Response> getStates(int countryId) => _mockResponse({'success': true, 'data': []});
  Future<Response> getCities(int stateId) => _mockResponse({'success': true, 'data': []});

  Future<Response> updateProfile({
    required String name,
    String? contact, String? dob, String? gender, String? address,
    int? countryId, int? stateId, int? cityId, String? photoPath,
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
      }
    }
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
      'photo': 'https://images.unsplash.com/photo-1521590832167-7bfc17484d20?w=500&q=80',
      'banner_photo': 'https://images.unsplash.com/photo-1521590832167-7bfc17484d20?w=500&q=80',
      'about': 'Premium salon offering the best styling and grooming services.',
      'listing': {
        'services': [{'name': 'Haircut'}, {'name': 'Coloring'}, {'name': 'Styling'}]
      }
    },
    {
      'id': 2,
      'name': 'Urban Trim Barbershop',
      'address': '456 Downtown Blvd',
      'city': {'name': 'Chicago'},
      'ratings_avg_rating': '4.9',
      'ratings_count': '340',
      'status': 1,
      'photo': 'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=500&q=80',
      'banner_photo': 'https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=500&q=80',
      'about': 'Classic cuts and modern styles for the urban gentleman.',
      'listing': {
        'services': [{'name': 'Fade'}, {'name': 'Beard Trim'}, {'name': 'Shave'}]
      }
    },
    {
      'id': 3,
      'name': 'Serenity Spa & Wellness',
      'address': '789 Quiet Lane',
      'city': {'name': 'Los Angeles'},
      'ratings_avg_rating': '4.7',
      'ratings_count': '89',
      'status': 1,
      'photo': 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=500&q=80',
      'banner_photo': 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=500&q=80',
      'about': 'Relax, rejuvenate, and refresh your mind and body.',
      'listing': {
        'services': [{'name': 'Massage'}, {'name': 'Facial'}, {'name': 'Manicure'}]
      }
    }
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
              'rating': 5
           },
           {
              'user': {'name': 'Michael Smith'},
              'comment': 'Great vibe, professional staff.',
              'rating': 4.5
           }
        ],
        'categories': [
           {'id': 1, 'name': 'Haircut', 'icon': 'https://cdn-icons-png.flaticon.com/512/3011/3011036.png'},
           {'id': 2, 'name': 'Makeup', 'icon': 'https://cdn-icons-png.flaticon.com/512/1940/1940986.png'},
           {'id': 3, 'name': 'Massage', 'icon': 'https://cdn-icons-png.flaticon.com/512/2950/2950993.png'},
           {'id': 4, 'name': 'Nails', 'icon': 'https://cdn-icons-png.flaticon.com/512/1940/1940947.png'},
        ]
      }
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
                'photo': 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=200&q=80'
              },
              {
                'id': 2,
                'name': 'Hair Coloring',
                'price': '80.00',
                'duration': 90,
                'photo': 'https://images.unsplash.com/photo-1519699047748-de8e457a634e?w=200&q=80'
              }
            ]
          }
        ],
        'staffs': [
          {'id': 1, 'name': 'Jane Doe', 'designation': 'Senior Stylist', 'photo': 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&q=80'},
          {'id': 2, 'name': 'Mark Roe', 'designation': 'Barber', 'photo': 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&q=80'}
        ],
        'reviews': [
           {'user': {'name': 'Alice'}, 'comment': 'Great!', 'rating': 5, 'created_at': '2024-05-12'}
        ]
      }
    });
  }

  Future<Response> exploreSalons({String? search, String? city, String? category, String? rating, String? sort}) {
    return _mockResponse({
      'success': true,
      'data': {
        'branches': {
          'data': _dummyBranches
        }
      }
    });
  }

  Future<Response> getBookings({String? status, String? from, String? to, String? q}) {
    return _mockResponse({
      'success': true,
      'data': {
        'bookings': {
          'data': []
        }
      }
    });
  }

  Future<Response> getNotifications() {
    return _mockResponse({
      'success': true,
      'data': {
        'notifications': {
          'data': [
             {'title': 'Booking Confirmed', 'message': 'Your booking for Glow & Glamour has been confirmed.', 'is_read': 0, 'created_at': '2 mins ago'}
          ]
        }
      }
    });
  }

  Future<Response> switchLanguage(String locale) => _mockResponse({'success': true});
  Future<Response> getBookingChat(int bookingId) => _mockResponse({'success': true, 'data': []});
  Future<Response> sendBookingChatMessage(int bookingId, {String? details, String? attachmentPath}) => _mockResponse({'success': true});
  Future<Response> markChatRead(int bookingId) => _mockResponse({'success': true});
}
