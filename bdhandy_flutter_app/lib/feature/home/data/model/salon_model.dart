// ───────────────────────────────────────────────
//  SaloJoy — Static Mock Data Models
// ───────────────────────────────────────────────

class Salon {
  final String id;
  final String listingId;
  final String slug;
  final String name;
  final String address;
  final String city;
  final double rating;
  final int reviewCount;
  final double distanceKm;
  final String openTime;
  final String closeTime;
  final bool isOpen;
  final String imageColor; // for placeholder gradient
  final List<String> tags;
  final String about;
  final String? imageUrl; // real photo URL from API

  const Salon({
    required this.id,
    required this.listingId,
    required this.slug,
    required this.name,
    required this.address,
    required this.city,
    required this.rating,
    required this.reviewCount,
    required this.distanceKm,
    required this.openTime,
    required this.closeTime,
    required this.isOpen,
    required this.imageColor,
    required this.tags,
    required this.about,
    this.imageUrl,
  });

  factory Salon.fromJson(Map<String, dynamic> json) {
    return Salon(
      id: json['id'] as String,
      listingId: json['listingId'] as String,
      slug: json['slug'] as String,
      name: json['name'] as String,
      address: json['address'] as String,
      city: json['city'] as String,
      rating: (json['rating'] as num).toDouble(),
      reviewCount: json['reviewCount'] as int,
      distanceKm: (json['distanceKm'] as num).toDouble(),
      openTime: json['openTime'] as String,
      closeTime: json['closeTime'] as String,
      isOpen: json['isOpen'] as bool,
      imageColor: json['imageColor'] as String,
      tags: List<String>.from(json['tags'] ?? []),
      about: json['about'] as String,
      imageUrl: json['imageUrl'] as String?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'listingId': listingId,
      'slug': slug,
      'name': name,
      'address': address,
      'city': city,
      'rating': rating,
      'reviewCount': reviewCount,
      'distanceKm': distanceKm,
      'openTime': openTime,
      'closeTime': closeTime,
      'isOpen': isOpen,
      'imageColor': imageColor,
      'tags': tags,
      'about': about,
      'imageUrl': imageUrl,
    };
  }
}

class SalonMaster {
  final String id;
  final String name;
  final String role;
  final double rating;
  final String experience;
  final String avatarInitials;
  final int avatarColor;
  final String? photoUrl;

  const SalonMaster({
    required this.id,
    required this.name,
    required this.role,
    required this.rating,
    required this.experience,
    required this.avatarInitials,
    required this.avatarColor,
    this.photoUrl,
  });

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is SalonMaster && runtimeType == other.runtimeType && id == other.id;

  @override
  int get hashCode => id.hashCode;
}

class SalonService {
  final String id;
  final String name;
  final String description;
  final int durationMins;
  final int price;
  final String category;
  final String iconEmoji;
  final String? imageUrl;

  const SalonService({
    required this.id,
    required this.name,
    required this.description,
    required this.durationMins,
    required this.price,
    required this.category,
    required this.iconEmoji,
    this.imageUrl,
  });

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is SalonService && runtimeType == other.runtimeType && id == other.id;

  @override
  int get hashCode => id.hashCode;
}

class Booking {
  final String id;
  final String salonName;
  final String salonAddress;
  final String serviceName;
  final String masterName;
  final DateTime dateTime;
  final int price;
  final String status; // 'upcoming' | 'completed' | 'cancelled'

  const Booking({
    required this.id,
    required this.salonName,
    required this.salonAddress,
    required this.serviceName,
    required this.masterName,
    required this.dateTime,
    required this.price,
    required this.status,
  });
}

// ───────────────────────────────────────────────
//  MOCK DATA
// ───────────────────────────────────────────────

class MockData {
  static const List<Salon> salons = [
    Salon(
      id: '1',
      listingId: '1',
      slug: 'the-luxe-salon',
      name: 'The Luxe Salon',
      address: 'Connaught Place, New Delhi',
      city: 'New Delhi',
      rating: 4.8,
      reviewCount: 138,
      distanceKm: 0.4,
      openTime: '9:00 AM',
      closeTime: '9:00 PM',
      isOpen: true,
      imageColor: '6C3EE8',
      tags: ['Hygiene', 'Top Rated', 'Premium Products'],
      about:
          'Experience luxury and redefining your style with our professional services and top experts. We combine modern techniques with premium products to deliver an unmatched salon experience.',
    ),
    Salon(
      id: '2',
      listingId: '2',
      slug: 'looks-studio',
      name: 'Looks Studio',
      address: 'Sanikhabra Road, New Delhi',
      city: 'New Delhi',
      rating: 4.5,
      reviewCount: 346,
      distanceKm: 0.7,
      openTime: '10:00 AM',
      closeTime: '8:00 PM',
      isOpen: true,
      imageColor: 'E83E8C',
      tags: ['Unisex', 'Trendy', 'Affordable'],
      about:
          'Looks Studio is your go-to destination for contemporary haircuts, color treatments and beauty services. Our expert stylists stay updated with the latest trends.',
    ),
    Salon(
      id: '3',
      listingId: '3',
      slug: 'essensuals-salon',
      name: 'Essensuals Salon',
      address: 'Hanuman Road, New Delhi',
      city: 'New Delhi',
      rating: 4.6,
      reviewCount: 110,
      distanceKm: 0.8,
      openTime: '9:30 AM',
      closeTime: '8:30 PM',
      isOpen: true,
      imageColor: '3EC8B4',
      tags: ['TONI&GUY', 'Expert Stylists', 'Global Trends'],
      about:
          'Essensuals by TONI&GUY brings you world-class hair and beauty services. Our internationally trained stylists deliver precision cuts and expert color.',
    ),
    Salon(
      id: '4',
      listingId: '4',
      slug: 'glam-go',
      name: 'Glam & Go',
      address: 'Lajpat Nagar, New Delhi',
      city: 'New Delhi',
      rating: 4.3,
      reviewCount: 89,
      distanceKm: 1.2,
      openTime: '10:00 AM',
      closeTime: '9:00 PM',
      isOpen: false,
      imageColor: 'FF9800',
      tags: ['Bridal', 'Makeup', 'Skin Care'],
      about:
          'Glam & Go specializes in bridal makeup, skin care and beauty treatments. We offer personalized beauty solutions for every occasion.',
    ),
  ];

  static const List<SalonMaster> masters = [
    SalonMaster(
      id: '1',
      name: 'Rohan Verma',
      role: 'Hair Stylist',
      rating: 4.8,
      experience: '8+ Yrs Exp',
      avatarInitials: 'RV',
      avatarColor: 0xFF6C3EE8,
    ),
    SalonMaster(
      id: '2',
      name: 'Anjali Sharma',
      role: 'Beauty Expert',
      rating: 4.8,
      experience: '5+ Yrs Exp',
      avatarInitials: 'AS',
      avatarColor: 0xFFE83E8C,
    ),
    SalonMaster(
      id: '3',
      name: 'Karan Malhotra',
      role: 'Hair Colorist',
      rating: 4.6,
      experience: '7+ Yrs Exp',
      avatarInitials: 'KM',
      avatarColor: 0xFF3EC8B4,
    ),
    SalonMaster(
      id: '4',
      name: 'Neha Kapoor',
      role: 'Makeup Artist',
      rating: 4.9,
      experience: '5+ Yrs Exp',
      avatarInitials: 'NK',
      avatarColor: 0xFFFF9800,
    ),
  ];

  static const List<SalonService> services = [
    // Hair
    SalonService(
      id: 'h1',
      name: 'Haircut (Men)',
      description: 'Stylish haircut with wash',
      durationMins: 30,
      price: 499,
      category: 'Hair',
      iconEmoji: '✂️',
    ),
    SalonService(
      id: 'h2',
      name: 'Haircut (Women)',
      description: 'Haircut with wash & blow dry',
      durationMins: 45,
      price: 799,
      category: 'Hair',
      iconEmoji: '✂️',
    ),
    SalonService(
      id: 'h3',
      name: 'Hair Spa',
      description: 'Nourishing hair spa treatment',
      durationMins: 60,
      price: 999,
      category: 'Hair',
      iconEmoji: '💆',
    ),
    SalonService(
      id: 'h4',
      name: 'Hair Color',
      description: 'Global / Highlights / Balayage',
      durationMins: 120,
      price: 2499,
      category: 'Hair',
      iconEmoji: '🎨',
    ),
    SalonService(
      id: 'h5',
      name: 'Smoothening',
      description: 'Frizz-free smoothening',
      durationMins: 150,
      price: 2999,
      category: 'Hair',
      iconEmoji: '✨',
    ),
    SalonService(
      id: 'h6',
      name: 'Beard Styling',
      description: 'Beard trim & styling',
      durationMins: 30,
      price: 349,
      category: 'Hair',
      iconEmoji: '🪒',
    ),
    // Beauty
    SalonService(
      id: 'b1',
      name: 'Facial',
      description: 'Deep cleansing facial',
      durationMins: 60,
      price: 799,
      category: 'Beauty',
      iconEmoji: '🌸',
    ),
    SalonService(
      id: 'b2',
      name: 'Waxing (Full Arms)',
      description: 'Smooth full arm waxing',
      durationMins: 30,
      price: 349,
      category: 'Beauty',
      iconEmoji: '🪷',
    ),
    SalonService(
      id: 'b3',
      name: 'Threading',
      description: 'Eyebrow & upper lip',
      durationMins: 15,
      price: 99,
      category: 'Beauty',
      iconEmoji: '🧵',
    ),
    SalonService(
      id: 'b4',
      name: 'Bleach',
      description: 'Face & neck bleach',
      durationMins: 30,
      price: 299,
      category: 'Beauty',
      iconEmoji: '💫',
    ),
    // Makeup
    SalonService(
      id: 'm1',
      name: 'Party Makeup',
      description: 'Full face party look',
      durationMins: 60,
      price: 1499,
      category: 'Makeup',
      iconEmoji: '💄',
    ),
    SalonService(
      id: 'm2',
      name: 'Bridal Makeup',
      description: 'Complete bridal package',
      durationMins: 120,
      price: 4999,
      category: 'Makeup',
      iconEmoji: '👰',
    ),
    // Nails
    SalonService(
      id: 'n1',
      name: 'Manicure',
      description: 'Classic manicure with polish',
      durationMins: 45,
      price: 399,
      category: 'Nails',
      iconEmoji: '💅',
    ),
    SalonService(
      id: 'n2',
      name: 'Pedicure',
      description: 'Relaxing foot pedicure',
      durationMins: 60,
      price: 499,
      category: 'Nails',
      iconEmoji: '🦶',
    ),
    SalonService(
      id: 'n3',
      name: 'Gel Nails',
      description: 'Long-lasting gel nail art',
      durationMins: 90,
      price: 999,
      category: 'Nails',
      iconEmoji: '🌟',
    ),
  ];

  static final List<Booking> bookings = [
    Booking(
      id: 'bk1',
      salonName: 'The Luxe Salon',
      salonAddress: 'Connaught Place, New Delhi',
      serviceName: 'Haircut (Men)',
      masterName: 'Rohan Verma',
      dateTime: DateTime(2024, 5, 20, 11, 0),
      price: 499,
      status: 'upcoming',
    ),
    Booking(
      id: 'bk2',
      salonName: 'Looks Studio',
      salonAddress: 'Sanikhabra Road, New Delhi',
      serviceName: 'Hair Spa',
      masterName: 'Anjali Sharma',
      dateTime: DateTime(2024, 6, 2, 16, 0),
      price: 999,
      status: 'upcoming',
    ),
    Booking(
      id: 'bk3',
      salonName: 'Essensuals Salon',
      salonAddress: 'Hanuman Road, New Delhi',
      serviceName: 'Hair Color',
      masterName: 'Karan Malhotra',
      dateTime: DateTime(2024, 6, 15, 14, 0),
      price: 2499,
      status: 'upcoming',
    ),
    Booking(
      id: 'bk4',
      salonName: 'The Luxe Salon',
      salonAddress: 'Connaught Place, New Delhi',
      serviceName: 'Beard Styling',
      masterName: 'Rohan Verma',
      dateTime: DateTime(2024, 4, 10, 10, 0),
      price: 349,
      status: 'completed',
    ),
    Booking(
      id: 'bk5',
      salonName: 'Glam & Go',
      salonAddress: 'Lajpat Nagar, New Delhi',
      serviceName: 'Facial',
      masterName: 'Neha Kapoor',
      dateTime: DateTime(2024, 3, 25, 15, 0),
      price: 799,
      status: 'cancelled',
    ),
  ];

  static const List<String> timeSlots = [
    '10:00 AM',
    '11:00 AM',
    '12:00 PM',
    '01:00 PM',
    '02:00 PM',
    '03:00 PM',
    '04:00 PM',
    '05:00 PM',
  ];

  static const String userName = 'Rahul';
  static const String userFullName = 'Rahul Sharma';
  static const String userPhone = '+91 98765 43210';
  static const int walletBalance = 1250;
}

class ServiceExtra {
  final int id;
  final String name;
  final double price;

  ServiceExtra({required this.id, required this.name, required this.price});

  factory ServiceExtra.fromJson(Map<String, dynamic> json) {
    return ServiceExtra(
      id: json['id'],
      name: json['name'],
      price: double.parse(json['default_price'].toString()),
    );
  }
}
