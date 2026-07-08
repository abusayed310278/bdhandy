import 'package:bdhandy_flutter_app/feature/profile/presentation/view/signup_screen_view.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'onboarding_screen.dart';

class SplashScreen extends StatelessWidget {
  const SplashScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,

      body: SafeArea(
        child: Column(
          children: [
            const SizedBox(height: 60),

            // Logo
            Image.asset(
              "assets/images/logo.png",
              width: 90,
              height: 90,
              errorBuilder: (context, error, stackTrace) => const Icon(
                Icons.handyman,
                size: 90,
                color: Color(0xff16B83E),
              ),
            ),

            const SizedBox(height: 15),

            // Brand Name
            RichText(
              text: const TextSpan(
                children: [
                  TextSpan(
                    text: "PICK",
                    style: TextStyle(
                      fontSize: 34,
                      fontWeight: FontWeight.bold,
                      color: Colors.black,
                    ),
                  ),

                  TextSpan(
                    text: "HANDY",
                    style: TextStyle(
                      fontSize: 34,
                      fontWeight: FontWeight.bold,
                      color: Color(0xff16B83E),
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 15),

            const Text(
              "Find trusted local service\nproviders near you",
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 16,
                color: Colors.black87,
                height: 1.5,
              ),
            ),

            const SizedBox(height: 30),

            // Illustration
            Expanded(
              child: Stack(
                alignment: Alignment.bottomCenter,
                children: [
                  Container(
                    height: 260,
                    decoration: const BoxDecoration(
                      color: Color(0xffF4FBF5),
                      borderRadius: BorderRadius.vertical(
                        top: Radius.circular(150),
                      ),
                    ),
                  ),

                  Positioned(
                    bottom: 20,
                    child: Image.asset(
                      "assets/images/logo.png",
                      height: 300,
                      errorBuilder: (context, error, stackTrace) => const Icon(
                        Icons.person,
                        size: 200,
                        color: Colors.grey,
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // Slider dots
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [dot(true), dot(false), dot(false)],
            ),

            const SizedBox(height: 25),

            // Button
            Container(
              margin: const EdgeInsets.symmetric(horizontal: 35),
              width: double.infinity,
              height: 55,

              child: ElevatedButton(
                onPressed: () {
                  Get.to(() => const OnboardingScreen());
                },

                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xff16B83E),

                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(30),
                  ),

                  elevation: 0,
                ),

                child: const Text(
                  "Get Started",
                  style: TextStyle(
                    fontSize: 17,
                    color: Colors.white,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ),

            const SizedBox(height: 20),

            const Text(
              "or continue with",
              style: TextStyle(color: Colors.grey, fontSize: 14),
            ),

            const SizedBox(height: 20),

            // Social Login
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                socialButton(Icons.g_mobiledata, "Google"),

                const SizedBox(width: 35),

                socialButton(Icons.facebook, "Facebook"),

                const SizedBox(width: 35),

                socialButton(Icons.apple, "Apple"),
              ],
            ),

            const SizedBox(height: 25),

            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text(
                  "Don't have an account? ",
                  style: TextStyle(color: Colors.black54),
                ),
                GestureDetector(
                  onTap: () {
                    Get.to(() => const SignupScreenView());
                  },
                  child: const Text(
                    "Sign Up",
                    style: TextStyle(
                      color: Color(0xff16B83E),
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 25),
          ],
        ),
      ),
    );
  }

  Widget dot(bool active) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 4),

      width: active ? 10 : 8,
      height: active ? 10 : 8,

      decoration: BoxDecoration(
        shape: BoxShape.circle,

        color: active ? const Color(0xff16B83E) : Colors.grey.shade300,
      ),
    );
  }

  Widget socialButton(IconData icon, String title) {
    return Column(
      children: [
        Container(
          width: 55,
          height: 55,

          decoration: BoxDecoration(
            shape: BoxShape.circle,

            color: Colors.white,

            boxShadow: [BoxShadow(color: Colors.grey.shade200, blurRadius: 10)],
          ),

          child: Icon(icon, size: 30, color: Colors.black),
        ),

        const SizedBox(height: 5),

        Text(title, style: const TextStyle(fontSize: 12)),
      ],
    );
  }
}
