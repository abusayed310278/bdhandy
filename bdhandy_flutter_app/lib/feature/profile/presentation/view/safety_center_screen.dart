import 'package:flutter/material.dart';

class SafetyCenterScreen extends StatelessWidget {
  const SafetyCenterScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.white,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.close),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text(
          "Safety Center",
          style: TextStyle(
            fontSize: 17,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 10),

              // Tabs
              Row(
                children: [
                  _tabItem("GUIDE", true),
                  _tabItem("TOOLS", false),
                  _tabItem("RESOURCES", false),
                ],
              ),

              const SizedBox(height: 30),

              // Greeting
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: const [
                      Text(
                        "Hi Customer",
                        style: TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      SizedBox(height: 6),
                      Text(
                        "Here's what you need to know about safety",
                        style: TextStyle(
                          color: Colors.grey,
                          fontSize: 14,
                        ),
                      ),
                    ],
                  ),
                  const CircleAvatar(
                    radius: 25,
                    backgroundColor: Color(0xffE8F1FF),
                    child: Icon(
                      Icons.person,
                      color: Color(0xff1677FF),
                    ),
                  )
                ],
              ),

              const SizedBox(height: 35),

              _sectionTitle("Reporting"),
              SafetyCard(
                icon: Icons.report_problem_outlined,
                color: Colors.orange.shade50,
                iconColor: Colors.orange,
                title: "Report a Concern",
                subtitle: "Know when and how to report unsafe behavior.",
              ),

              const SizedBox(height: 25),

              _sectionTitle("Provider Safety"),
              SafetyCard(
                icon: Icons.verified_user_outlined,
                color: Colors.blue.shade50,
                iconColor: Colors.blue,
                title: "Verified Providers",
                subtitle: "Learn how BDHANDY verifies providers.",
              ),

              const SizedBox(height: 25),

              _sectionTitle("Booking Safety"),
              SafetyCard(
                icon: Icons.lock_outline,
                color: Colors.green.shade50,
                iconColor: Colors.green,
                title: "Safe Booking Guide",
                subtitle: "Tips for safe communication and payments.",
              ),

              const SizedBox(height: 25),

              _sectionTitle("Emergency"),
              SafetyCard(
                icon: Icons.support_agent_outlined,
                color: Colors.red.shade50,
                iconColor: Colors.red,
                title: "Contact Support",
                subtitle: "Get help from BDHANDY support team.",
              ),

              const SizedBox(height: 40),

              // Emergency Button
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: const Color(0xfffff1f1),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      "Need immediate help?",
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 8),
                    const Text(
                      "Report a safety issue and our team will review it.",
                      style: TextStyle(
                        color: Colors.grey,
                      ),
                    ),
                    const SizedBox(height: 15),
                    ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.red,
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      onPressed: () {},
                      child: const Text("Report Incident"),
                    )
                  ],
                ),
              ),

              const SizedBox(height: 30),
            ],
          ),
        ),
      ),
    );
  }

  Widget _tabItem(String text, bool active) {
    return Expanded(
      child: Column(
        children: [
          Text(
            text,
            style: TextStyle(
              fontSize: 12,
              fontWeight: active ? FontWeight.bold : FontWeight.w500,
              color: active ? Colors.black : Colors.grey,
            ),
          ),
          const SizedBox(height: 10),
          Container(
            height: 2,
            color: active ? Colors.black : Colors.transparent,
          )
        ],
      ),
    );
  }

  Widget _sectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Text(
        title,
        style: const TextStyle(
          fontSize: 18,
          fontWeight: FontWeight.w800,
        ),
      ),
    );
  }
}

class SafetyCard extends StatelessWidget {
  final IconData icon;
  final Color color;
  final Color iconColor;
  final String title;
  final String subtitle;

  const SafetyCard({
    super.key,
    required this.icon,
    required this.color,
    required this.iconColor,
    required this.title,
    required this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
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
          Container(
            height: 60,
            width: 60,
            decoration: BoxDecoration(
              color: color,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Icon(
              icon,
              color: iconColor,
              size: 30,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  subtitle,
                  style: const TextStyle(
                    fontSize: 13,
                    color: Colors.grey,
                  ),
                ),
              ],
            ),
          ),
          const Icon(
            Icons.arrow_forward_ios,
            size: 16,
            color: Colors.grey,
          )
        ],
      ),
    );
  }
}
