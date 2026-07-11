// import 'package:bdhandy_flutter_app/feature/profile/presentation/view/signup_screen_view.dart';
// import 'package:flutter/material.dart';
// import 'package:get/get.dart';
// import 'onboarding_screen.dart';

// class SplashScreen extends StatelessWidget {
//   const SplashScreen({super.key});

//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//       backgroundColor: Colors.white,

//       body: SafeArea(
//         child: Column(
//           children: [
//             const SizedBox(height: 60),

//             // Logo
//             Image.asset(
//               "assets/images/logo.png",
//               height: 60,
//               fit: BoxFit.contain,
//               errorBuilder: (context, error, stackTrace) => const Icon(
//                 Icons.handyman,
//                 size: 60,
//                 color: Color(0xff16B83E),
//               ),
//             ),

//             const SizedBox(height: 15),

//             // Brand Name
//             RichText(
//               text: const TextSpan(
//                 children: [
//                   TextSpan(
//                     text: "PICK",
//                     style: TextStyle(
//                       fontSize: 34,
//                       fontWeight: FontWeight.bold,
//                       color: Colors.black,
//                     ),
//                   ),

//                   TextSpan(
//                     text: "HANDY",
//                     style: TextStyle(
//                       fontSize: 34,
//                       fontWeight: FontWeight.bold,
//                       color: Color(0xff16B83E),
//                     ),
//                   ),
//                 ],
//               ),
//             ),

//             const SizedBox(height: 15),

//             const Text(
//               "Find trusted local service\nproviders near you",
//               textAlign: TextAlign.center,
//               style: TextStyle(
//                 fontSize: 16,
//                 color: Colors.black87,
//                 height: 1.5,
//               ),
//             ),

//             const SizedBox(height: 30),

//             // Illustration
//             Expanded(
//               child: Stack(
//                 alignment: Alignment.bottomCenter,
//                 children: [
//                   Container(
//                     height: 260,
//                     decoration: const BoxDecoration(
//                       color: Color(0xffF4FBF5),
//                       borderRadius: BorderRadius.vertical(
//                         top: Radius.circular(150),
//                       ),
//                     ),
//                   ),

//                   Positioned(
//                     bottom: 20,
//                     left: 0,
//                     right: 0,
//                     child: Container(
//                       height: 280,
//                       alignment: Alignment.bottomCenter,
//                       child: Image.asset(
//                         "assets/images/logo.png",
//                         fit: BoxFit.contain,
//                         errorBuilder: (context, error, stackTrace) => const Icon(
//                           Icons.person,
//                           size: 200,
//                           color: Colors.grey,
//                         ),
//                       ),
//                     ),
//                   ),
//                 ],
//               ),
//             ),

//             // Slider dots
//             Row(
//               mainAxisAlignment: MainAxisAlignment.center,
//               children: [dot(true), dot(false), dot(false)],
//             ),

//             const SizedBox(height: 25),

//             // Button
//             Container(
//               margin: const EdgeInsets.symmetric(horizontal: 35),
//               width: double.infinity,
//               height: 55,

//               child: ElevatedButton(
//                 onPressed: () {
//                   Get.to(() => const OnboardingScreen());
//                 },

//                 style: ElevatedButton.styleFrom(
//                   backgroundColor: const Color(0xff16B83E),

//                   shape: RoundedRectangleBorder(
//                     borderRadius: BorderRadius.circular(30),
//                   ),

//                   elevation: 0,
//                 ),

//                 child: const Text(
//                   "Get Started",
//                   style: TextStyle(
//                     fontSize: 17,
//                     color: Colors.white,
//                     fontWeight: FontWeight.w600,
//                   ),
//                 ),
//               ),
//             ),

//             const SizedBox(height: 20),

//             const Text(
//               "or continue with",
//               style: TextStyle(color: Colors.grey, fontSize: 14),
//             ),

//             const SizedBox(height: 20),

//             // Social Login
//             Row(
//               mainAxisAlignment: MainAxisAlignment.center,
//               children: [
//                 socialButton(Icons.g_mobiledata, "Google"),

//                 const SizedBox(width: 35),

//                 socialButton(Icons.facebook, "Facebook"),

//                 const SizedBox(width: 35),

//                 socialButton(Icons.apple, "Apple"),
//               ],
//             ),

//             const SizedBox(height: 25),

//             Row(
//               mainAxisAlignment: MainAxisAlignment.center,
//               children: [
//                 const Text(
//                   "Don't have an account? ",
//                   style: TextStyle(color: Colors.black54),
//                 ),
//                 GestureDetector(
//                   onTap: () {
//                     Get.to(() => const SignupScreenView());
//                   },
//                   child: const Text(
//                     "Sign Up",
//                     style: TextStyle(
//                       color: Color(0xff16B83E),
//                       fontWeight: FontWeight.bold,
//                     ),
//                   ),
//                 ),
//               ],
//             ),

//             const SizedBox(height: 25),
//           ],
//         ),
//       ),
//     );
//   }

//   Widget dot(bool active) {
//     return Container(
//       margin: const EdgeInsets.symmetric(horizontal: 4),

//       width: active ? 10 : 8,
//       height: active ? 10 : 8,

//       decoration: BoxDecoration(
//         shape: BoxShape.circle,

//         color: active ? const Color(0xff16B83E) : Colors.grey.shade300,
//       ),
//     );
//   }

//   Widget socialButton(IconData icon, String title) {
//     return Column(
//       children: [
//         Container(
//           width: 55,
//           height: 55,

//           decoration: BoxDecoration(
//             shape: BoxShape.circle,

//             color: Colors.white,

//             boxShadow: [BoxShadow(color: Colors.grey.shade200, blurRadius: 10)],
//           ),

//           child: Icon(icon, size: 30, color: Colors.black),
//         ),

//         const SizedBox(height: 5),

//         Text(title, style: const TextStyle(fontSize: 12)),
//       ],
//     );
//   }
// }

import 'package:bdhandy_flutter_app/feature/profile/presentation/view/signup_screen_view.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

import 'onboarding_screen.dart';

class SplashScreen extends StatelessWidget {
  const SplashScreen({super.key});

  static const Color primaryGreen = Color(0xFF16B83E);
  static const Color lightGreenBackground = Color(0xFFF4FBF5);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Column(
          children: [
            const SizedBox(height: 25),

            // App logo
            Image.asset(
              'assets/images/logo.png',
              width: 70,
              height: 70,
              fit: BoxFit.contain,
              errorBuilder: (context, error, stackTrace) {
                return const Icon(
                  Icons.handyman_rounded,
                  size: 65,
                  color: primaryGreen,
                );
              },
            ),

            const SizedBox(height: 10),

            // Brand name
            RichText(
              text: TextSpan(
                children: [
                  TextSpan(
                    text: 'PICK',
                    style: TextStyle(
                      fontSize: 32,
                      fontWeight: FontWeight.w700,
                      color: Colors.black,
                      letterSpacing: 0.5,
                    ),
                  ),
                  TextSpan(
                    text: 'HANDY',
                    style: TextStyle(
                      fontSize: 32,
                      fontWeight: FontWeight.w700,
                      color: primaryGreen,
                      letterSpacing: 0.5,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 10),

            const Text(
              'Find trusted local service\nproviders near you',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 15,
                color: Colors.black54,
                height: 1.4,
              ),
            ),

            const SizedBox(height: 15),

            // Main illustration area
            Expanded(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 18),
                child: LayoutBuilder(
                  builder: (context, constraints) {
                    final imageHeight = (constraints.maxHeight * 0.88)
                        .clamp(170.0, 300.0)
                        .toDouble();

                    final backgroundHeight = (imageHeight * 0.78)
                        .clamp(150.0, 250.0)
                        .toDouble();

                    return Stack(
                      alignment: Alignment.bottomCenter,
                      children: [
                        // Soft background shape
                        Positioned(
                          left: 0,
                          right: 0,
                          bottom: 0,
                          child: Container(
                            height: backgroundHeight,
                            decoration: const BoxDecoration(
                              color: lightGreenBackground,
                              borderRadius: BorderRadius.only(
                                topLeft: Radius.circular(170),
                                topRight: Radius.circular(170),
                                bottomLeft: Radius.circular(25),
                                bottomRight: Radius.circular(25),
                              ),
                            ),
                          ),
                        ),

                        // Separate illustration image
                        Positioned(
                          left: 10,
                          right: 10,
                          bottom: 8,
                          child: SizedBox(
                            height: imageHeight,
                            child: Image.asset(
                              'assets/images/logo.png',

                              // Maintains original image proportions
                              fit: BoxFit.contain,

                              alignment: Alignment.bottomCenter,

                              errorBuilder: (context, error, stackTrace) {
                                return const Center(
                                  child: Icon(
                                    Icons.home_repair_service_rounded,
                                    size: 150,
                                    color: primaryGreen,
                                  ),
                                );
                              },
                            ),
                          ),
                        ),
                      ],
                    );
                  },
                ),
              ),
            ),

            const SizedBox(height: 12),

            // Slider dots
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                _dot(active: true),
                _dot(active: false),
                _dot(active: false),
              ],
            ),

            const SizedBox(height: 18),

            // Get Started button
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 35),
              child: SizedBox(
                width: double.infinity,
                height: 54,
                child: ElevatedButton(
                  onPressed: () {
                    Get.to(() => const OnboardingScreen());
                  },
                  style: ElevatedButton.styleFrom(
                    elevation: 0,
                    backgroundColor: primaryGreen,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(30),
                    ),
                  ),
                  child: const Text(
                    'Get Started',
                    style: TextStyle(fontSize: 17, fontWeight: FontWeight.w600),
                  ),
                ),
              ),
            ),

            const SizedBox(height: 16),

            const Text(
              'or continue with',
              style: TextStyle(color: Colors.grey, fontSize: 13),
            ),

            const SizedBox(height: 15),

            // Social login buttons
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                _socialButton(icon: Icons.g_mobiledata, title: 'Google'),
                const SizedBox(width: 32),
                _socialButton(icon: Icons.facebook, title: 'Facebook'),
                const SizedBox(width: 32),
                _socialButton(icon: Icons.apple, title: 'Apple'),
              ],
            ),

            const SizedBox(height: 18),

            // Sign-up option
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text(
                  "Don't have an account? ",
                  style: TextStyle(color: Colors.black54, fontSize: 14),
                ),
                GestureDetector(
                  onTap: () {
                    Get.to(() => const SignupScreenView());
                  },
                  child: const Text(
                    'Sign Up',
                    style: TextStyle(
                      color: primaryGreen,
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  Widget _dot({required bool active}) {
    return AnimatedContainer(
      duration: const Duration(milliseconds: 250),
      margin: const EdgeInsets.symmetric(horizontal: 4),
      width: active ? 10 : 8,
      height: active ? 10 : 8,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: active ? primaryGreen : Colors.grey.shade300,
      ),
    );
  }

  Widget _socialButton({required IconData icon, required String title}) {
    return Column(
      children: [
        Container(
          width: 52,
          height: 52,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: Colors.white,
            border: Border.all(color: Colors.grey.shade200),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.06),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Icon(icon, size: 29, color: Colors.black87),
        ),
        const SizedBox(height: 5),
        Text(
          title,
          style: const TextStyle(fontSize: 11, color: Colors.black54),
        ),
      ],
    );
  }
}
