import 'package:flutter/material.dart';
import 'package:get/get.dart';

class SearchScreen extends StatefulWidget {
  const SearchScreen({super.key});

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  final TextEditingController searchController = TextEditingController();

  String selectedCategory = 'Any';
  String selectedRating = 'Any';
  String selectedProviderType = 'Any';
  String selectedSort = 'Highest rated';

  bool verifiedOnly = false;
  bool emergencyAvailable = false;

  // Empty list to trigger the "No providers found" state
  final List<Map<String, dynamic>> providers = [];

  @override
  void dispose() {
    searchController.dispose();
    super.dispose();
  }

  void _clearFilters() {
    setState(() {
      searchController.clear();
      selectedCategory = 'Any';
      selectedRating = 'Any';
      selectedProviderType = 'Any';
      selectedSort = 'Highest rated';
      verifiedOnly = false;
      emergencyAvailable = false;
    });
  }

  void _applyFilters() {
    setState(() {});
    Get.back(); // Close the filter popup if it's open
  }

  void _showFilterPopup(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return StatefulBuilder(
          builder: (BuildContext context, StateSetter setModalState) {
            return Container(
              height: MediaQuery.of(context).size.height * 0.85,
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
              ),
              child: Column(
                children: [
                  // Handle bar
                  Center(
                    child: Container(
                      margin: const EdgeInsets.only(top: 12, bottom: 8),
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade300,
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                  ),
                  
                  // Header
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 8),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Filter Results',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.w800,
                            color: Color(0xff0F172A),
                          ),
                        ),
                        TextButton(
                          onPressed: () {
                            setModalState(() {
                              selectedCategory = 'Any';
                              selectedRating = 'Any';
                              selectedProviderType = 'Any';
                              verifiedOnly = false;
                              emergencyAvailable = false;
                            });
                            _clearFilters();
                          },
                          child: const Text(
                            'Clear',
                            style: TextStyle(
                              fontSize: 14,
                              color: Color(0xff94A3B8),
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const Divider(),
                  
                  // Filter content
                  Expanded(
                    child: SingleChildScrollView(
                      padding: const EdgeInsets.all(24),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildLabel('Category'),
                          _buildFilterDropdown(
                            value: selectedCategory,
                            items: const ['Any', 'Electrician', 'Plumbing', 'Cleaning', 'Interior Design', 'Painting', 'Wallpaper Installation'],
                            onChanged: (value) {
                              setModalState(() => selectedCategory = value!);
                            },
                          ),
                          const SizedBox(height: 20),

                          _buildLabel('Minimum Rating'),
                          _buildFilterDropdown(
                            value: selectedRating,
                            items: const ['Any', '5 Stars', '4 Stars & up', '3 Stars & up', '2 Stars & up'],
                            onChanged: (value) {
                              setModalState(() => selectedRating = value!);
                            },
                          ),
                          const SizedBox(height: 20),

                          _buildLabel('Provider type'),
                          _buildFilterDropdown(
                            value: selectedProviderType,
                            items: const ['Any', 'Individual', 'Company', 'Agency', 'Verified Expert'],
                            onChanged: (value) {
                              setModalState(() => selectedProviderType = value!);
                            },
                          ),
                          const SizedBox(height: 20),

                          _buildCheckBoxRow(
                            title: 'Verified only',
                            value: verifiedOnly,
                            onChanged: (value) {
                              setModalState(() => verifiedOnly = value ?? false);
                            },
                          ),
                          const SizedBox(height: 12),
                          _buildCheckBoxRow(
                            title: 'Emergency available',
                            value: emergencyAvailable,
                            onChanged: (value) {
                              setModalState(() => emergencyAvailable = value ?? false);
                            },
                          ),
                          const SizedBox(height: 24),

                          SizedBox(
                            width: double.infinity,
                            height: 48,
                            child: OutlinedButton.icon(
                              onPressed: () {
                                Get.snackbar('Location', 'Use my location action here', snackPosition: SnackPosition.BOTTOM);
                              },
                              icon: const Icon(Icons.location_on_outlined, size: 20, color: Color(0xff1293E3)),
                              label: const Text(
                                'Use my location',
                                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w600, color: Color(0xff334155)),
                              ),
                              style: OutlinedButton.styleFrom(
                                side: const BorderSide(color: Color(0xffDDE6F0)),
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),

                  // Apply button sticky at bottom
                  Container(
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      boxShadow: [
                        BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, -5))
                      ],
                    ),
                    child: SizedBox(
                      width: double.infinity,
                      height: 50,
                      child: ElevatedButton(
                        onPressed: () {
                          setState(() {}); // Update main screen state
                          Get.back(); // Close modal
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xff1293E3),
                          foregroundColor: Colors.white,
                          elevation: 0,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                        child: const Text(
                          'Apply Filters',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  void _showSortPopup(BuildContext context) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (context) {
        return Container(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Sort by',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Color(0xff0F172A)),
              ),
              const SizedBox(height: 16),
              ...['Highest rated', 'Lowest price', 'Most experienced', 'Nearest'].map((sortOption) {
                return ListTile(
                  contentPadding: EdgeInsets.zero,
                  title: Text(
                    sortOption,
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: selectedSort == sortOption ? FontWeight.bold : FontWeight.normal,
                      color: selectedSort == sortOption ? const Color(0xff1293E3) : const Color(0xff334155),
                    ),
                  ),
                  trailing: selectedSort == sortOption ? const Icon(Icons.check, color: Color(0xff1293E3)) : null,
                  onTap: () {
                    setState(() => selectedSort = sortOption);
                    Get.back();
                  },
                );
              }),
              const SizedBox(height: 16),
            ],
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xffF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, color: Color(0xff0F172A), size: 20),
          onPressed: () => Get.back(),
        ),
        title: const Text(
          'Find Providers',
          style: TextStyle(
            color: Color(0xff0F172A),
            fontSize: 18,
            fontWeight: FontWeight.w800,
          ),
        ),
      ),
      body: Column(
        children: [
          Container(
            color: Colors.white,
            padding: const EdgeInsets.fromLTRB(20, 10, 20, 20),
            child: Column(
              children: [
                // Search Input
                Container(
                  height: 48,
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: const Color(0xffE2E8F0)),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.02),
                        blurRadius: 6,
                        offset: const Offset(0, 2),
                      ),
                    ],
                  ),
                  child: TextField(
                    controller: searchController,
                    decoration: const InputDecoration(
                      hintText: 'What do you need?',
                      hintStyle: TextStyle(color: Color(0xff94A3B8), fontSize: 14),
                      prefixIcon: Icon(Icons.search, color: Color(0xff94A3B8), size: 20),
                      border: InputBorder.none,
                      contentPadding: EdgeInsets.symmetric(vertical: 14),
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                
                // Filter & Sort Buttons
                Row(
                  children: [
                    Expanded(
                      child: GestureDetector(
                        onTap: () => _showFilterPopup(context),
                        child: Container(
                          height: 42,
                          decoration: BoxDecoration(
                            color: Colors.white,
                            border: Border.all(color: const Color(0xffE2E8F0)),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Icon(Icons.tune, size: 18, color: Color(0xff1293E3)),
                              const SizedBox(width: 8),
                              const Text(
                                'Filter',
                                style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w700,
                                  color: Color(0xff334155),
                                ),
                              ),
                              if (selectedCategory != 'Any' || selectedRating != 'Any') ...[
                                const SizedBox(width: 6),
                                Container(
                                  padding: const EdgeInsets.all(4),
                                  decoration: const BoxDecoration(
                                    color: Color(0xff1293E3),
                                    shape: BoxShape.circle,
                                  ),
                                  child: const Text('1', style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)),
                                )
                              ]
                            ],
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: GestureDetector(
                        onTap: () => _showSortPopup(context),
                        child: Container(
                          height: 42,
                          decoration: BoxDecoration(
                            color: Colors.white,
                            border: Border.all(color: const Color(0xffE2E8F0)),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Icon(Icons.sort, size: 18, color: Color(0xff334155)),
                              const SizedBox(width: 8),
                              Text(
                                selectedSort == 'Highest rated' ? 'Sort' : selectedSort,
                                style: const TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w700,
                                  color: Color(0xff334155),
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          
          Expanded(
            child: providers.isEmpty ? _buildEmptyState() : _buildProviderList(),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: const Color(0xffF1F5F9),
                borderRadius: BorderRadius.circular(20),
              ),
              child: const Icon(
                Icons.manage_search,
                size: 42,
                color: Color(0xff94A3B8),
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'No providers found',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.w800,
                color: Color(0xff0F172A),
              ),
            ),
            const SizedBox(height: 12),
            const Text(
              'No providers found. Try adjusting your filters or searching for something else.',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 14,
                color: Color(0xff64748B),
                fontWeight: FontWeight.w500,
                height: 1.5,
              ),
            ),
            const SizedBox(height: 32),
            SizedBox(
              height: 44,
              child: ElevatedButton(
                onPressed: _clearFilters,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xff1293E3),
                  foregroundColor: Colors.white,
                  elevation: 0,
                  padding: const EdgeInsets.symmetric(horizontal: 24),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
                child: const Text(
                  'Clear filters',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildProviderList() {
    return ListView.separated(
      padding: const EdgeInsets.all(20),
      itemCount: providers.length,
      separatorBuilder: (_, __) => const SizedBox(height: 16),
      itemBuilder: (context, index) {
        final provider = providers[index];
        return _buildProviderCard(
          name: provider['name'] ?? '',
          rating: provider['rating'] ?? '',
          experience: provider['experience'] ?? '',
          location: provider['location'] ?? '',
          price: provider['price'] ?? '',
          imagePath: provider['image'] ?? 'assets/images/handyman.png',
        );
      },
    );
  }

  Widget _buildProviderCard({
    required String name,
    required String rating,
    required String experience,
    required String location,
    required String price,
    required String imagePath,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xffF1F5F9)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 15,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 72,
            height: 72,
            decoration: const BoxDecoration(
              color: Color(0xffF8FAFC),
              shape: BoxShape.circle,
            ),
            child: ClipOval(
              child: Image.asset(
                imagePath,
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) {
                  return const Icon(
                    Icons.person,
                    color: Color(0xff94A3B8),
                    size: 36,
                  );
                },
              ),
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(
                        name,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w800,
                          color: Color(0xff0F172A),
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    Icon(
                      Icons.favorite_border,
                      color: Colors.grey.shade400,
                      size: 20,
                    ),
                  ],
                ),
                const SizedBox(height: 6),
                Row(
                  children: [
                    const Icon(Icons.star, color: Colors.amber, size: 16),
                    const SizedBox(width: 4),
                    Text(
                      rating,
                      style: const TextStyle(
                        fontSize: 13,
                        color: Color(0xff64748B),
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 6),
                Text(
                  experience,
                  style: const TextStyle(
                    fontSize: 13,
                    color: Color(0xff64748B),
                  ),
                ),
                const SizedBox(height: 6),
                Row(
                  children: [
                    const Icon(Icons.location_on_outlined, color: Color(0xff94A3B8), size: 15),
                    const SizedBox(width: 4),
                    Text(
                      location,
                      style: const TextStyle(
                        fontSize: 13,
                        color: Color(0xff64748B),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 14),
                Row(
                  children: [
                    Text(
                      price,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w800,
                        color: Color(0xff1293E3),
                      ),
                    ),
                    const Spacer(),
                    SizedBox(
                      height: 36,
                      child: ElevatedButton(
                        onPressed: () {},
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xff1293E3),
                          foregroundColor: Colors.white,
                          elevation: 0,
                          padding: const EdgeInsets.symmetric(horizontal: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(10),
                          ),
                        ),
                        child: const Text(
                          'Book Now',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
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

  Widget _buildLabel(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Text(
        text,
        style: const TextStyle(
          fontSize: 14,
          color: Color(0xff334155),
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  Widget _buildFilterDropdown({
    required String value,
    required List<String> items,
    required ValueChanged<String?> onChanged,
  }) {
    return SizedBox(
      height: 48,
      child: DropdownButtonFormField<String>(
        value: value,
        isExpanded: true,
        icon: const Icon(Icons.keyboard_arrow_down, color: Color(0xff64748B), size: 22),
        decoration: InputDecoration(
          filled: true,
          fillColor: Colors.white,
          contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: const BorderSide(color: Color(0xffE2E8F0)),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: const BorderSide(color: Color(0xff1293E3), width: 1.5),
          ),
        ),
        style: const TextStyle(
          fontSize: 15,
          color: Color(0xff0F172A),
          fontWeight: FontWeight.w500,
        ),
        items: items.map((item) => DropdownMenuItem<String>(value: item, child: Text(item))).toList(),
        onChanged: onChanged,
      ),
    );
  }

  Widget _buildCheckBoxRow({
    required String title,
    required bool value,
    required ValueChanged<bool?> onChanged,
  }) {
    return InkWell(
      onTap: () => onChanged(!value),
      borderRadius: BorderRadius.circular(8),
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 8),
        child: Row(
          children: [
            SizedBox(
              width: 20,
              height: 20,
              child: Checkbox(
                value: value,
                onChanged: onChanged,
                materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                side: const BorderSide(color: Color(0xff94A3B8), width: 1.5),
                activeColor: const Color(0xff1293E3),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(4)),
              ),
            ),
            const SizedBox(width: 12),
            Text(
              title,
              style: const TextStyle(
                fontSize: 15,
                color: Color(0xff334155),
                fontWeight: FontWeight.w500,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
