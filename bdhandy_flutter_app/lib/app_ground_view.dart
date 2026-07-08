import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'core/colors/app_color.dart';
import 'core/common/widgets/bottomNavBar.dart';
import 'feature/home/presentation/controller/home_controller.dart';

import 'feature/home/presentation/view/home_screen.dart';
import 'feature/home/presentation/view/search_screen.dart';
import 'feature/booking/presentation/view/bookings_list_screen.dart';
import 'feature/chat/presentation/view/chat_list_screen.dart';
import 'feature/profile/presentation/view/profile_screen_view.dart';

class AppGroundView extends StatelessWidget {
  const AppGroundView({super.key});

  static const List<Widget> _tabs = [
    HomeScreen(),
    SearchScreen(),
    BookingsListScreen(),
    ChatListScreen(),
    ProfileScreenView(),
  ];

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<HomeController>();

    return Obx(
      () => Scaffold(
        backgroundColor: AppColor.background,
        body: IndexedStack(
          index: controller.currentIndex.value,
          children: _tabs,
        ),
        bottomNavigationBar: BottomNavBar(
          currentIndex: controller.currentIndex.value,
          onTap: (index) => controller.currentIndex.value = index,
        ),
      ),
    );
  }
}
