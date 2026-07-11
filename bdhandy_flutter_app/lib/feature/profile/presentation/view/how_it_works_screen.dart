import 'package:flutter/material.dart';

class HowItWorksScreen extends StatelessWidget {
  const HowItWorksScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xffF8FAFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(
            Icons.arrow_back_ios_new,
            color: Colors.black,
          ),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text(
          "How BDHANDY Works",
          style: TextStyle(
            color: Colors.black,
            fontSize: 18,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            // Header
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(25),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(24),
                gradient: const LinearGradient(
                  colors: [
                    Color(0xff1689FF),
                    Color(0xff4DB3FF),
                  ],
                ),
              ),
              child: Column(
                children: const [
                  Icon(
                    Icons.handyman_outlined,
                    size: 45,
                    color: Colors.white,
                  ),
                  SizedBox(height: 15),
                  Text(
                    "Find Trusted Professionals",
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 23,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                  SizedBox(height: 8),
                  Text(
                    "Book local services quickly,\neasily and safely.",
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: Colors.white70,
                      fontSize: 14,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 35),

            const Text(
              "Get Your Service In 3 Simple Steps",
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.w800,
              ),
            ),

            const SizedBox(height: 25),

            const StepCard(
              number: "01",
              icon: Icons.edit_note_outlined,
              title: "Post Your Requirement",
              description:
                  "Tell us what service you need, add your location and budget.",
            ),

            const TimelineLine(),

            const StepCard(
              number: "02",
              icon: Icons.chat_bubble_outline,
              title: "Receive Proposals",
              description:
                  "Verified providers send offers. Compare and choose the best one.",
            ),

            const TimelineLine(),

            const StepCard(
              number: "03",
              icon: Icons.check_circle_outline,
              title: "Get The Job Done",
              description:
                  "Confirm your booking and complete your service safely.",
            ),

            const SizedBox(height: 35),

            // Trust Card
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(.05),
                    blurRadius: 15,
                    offset: const Offset(0, 5),
                  )
                ],
              ),
              child: Column(
                children: const [
                  Text(
                    "Why Choose BDHANDY?",
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                  SizedBox(height: 20),
                  FeatureItem(
                    icon: Icons.verified_user_outlined,
                    text: "Verified Providers",
                  ),
                  FeatureItem(
                    icon: Icons.price_check_outlined,
                    text: "Transparent Pricing",
                  ),
                  FeatureItem(
                    icon: Icons.lock_outline,
                    text: "Secure Payments",
                  ),
                  FeatureItem(
                    icon: Icons.support_agent_outlined,
                    text: "24/7 Support",
                  ),
                ],
              ),
            ),

            const SizedBox(height: 30),

            SizedBox(
              width: double.infinity,
              height: 55,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xff1689FF),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                ),
                onPressed: () {},
                child: const Text(
                  "Get Started Free  →",
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ),

            const SizedBox(height: 30),
          ],
        ),
      ),
    );
  }
}

class StepCard extends StatelessWidget {
  final String number;
  final IconData icon;
  final String title;
  final String description;

  const StepCard({
    super.key,
    required this.number,
    required this.icon,
    required this.title,
    required this.description,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(.06),
            blurRadius: 15,
            offset: const Offset(0, 6),
          )
        ],
      ),
      child: Row(
        children: [
          Column(
            children: [
              Container(
                height: 55,
                width: 55,
                decoration: BoxDecoration(
                  color: const Color(0xffE8F3FF),
                  borderRadius: BorderRadius.circular(18),
                ),
                child: Icon(
                  icon,
                  color: const Color(0xff1689FF),
                  size: 28,
                ),
              ),
              const SizedBox(height: 5),
              Text(
                number,
                style: const TextStyle(
                  color: Color(0xff1689FF),
                  fontWeight: FontWeight.w800,
                ),
              )
            ],
          ),
          const SizedBox(width: 18),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 17,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  description,
                  style: const TextStyle(
                    color: Colors.grey,
                    fontSize: 13,
                    height: 1.4,
                  ),
                )
              ],
            ),
          )
        ],
      ),
    );
  }
}

class TimelineLine extends StatelessWidget {
  const TimelineLine({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 35,
      width: 2,
      color: const Color(0xff1689FF),
    );
  }
}

class FeatureItem extends StatelessWidget {
  final IconData icon;
  final String text;

  const FeatureItem({
    super.key,
    required this.icon,
    required this.text,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Row(
        children: [
          Container(
            height: 35,
            width: 35,
            decoration: BoxDecoration(
              color: const Color(0xffE8F3FF),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(
              icon,
              size: 20,
              color: const Color(0xff1689FF),
            ),
          ),
          const SizedBox(width: 12),
          Text(
            text,
            style: const TextStyle(
              fontWeight: FontWeight.w600,
              fontSize: 14,
            ),
          )
        ],
      ),
    );
  }
}
