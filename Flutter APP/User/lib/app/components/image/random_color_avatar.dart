import 'dart:math';
import 'package:flutter/material.dart';

final List<Color> avatarColors = [
  Colors.red, // A
  Colors.green, // B
  Colors.blue, // C
  Colors.orange, // D
  Colors.purple, // E
  Colors.teal, // F
  Colors.yellow, // G
  Colors.pink, // H
  Colors.brown, // I
  Colors.indigo, // J
  Colors.cyan, // K
  Colors.lime, // L
  Colors.amber, // M
  Colors.deepOrange, // N
  Colors.deepPurple, // O
  Colors.lightGreen, // P
  Colors.lightBlue, // Q
  Colors.lightBlueAccent, // R
  Colors.blueGrey, // S
  Colors.grey, // T
  Colors.greenAccent, // U
  Colors.purpleAccent, // V
  Colors.blueAccent, // W
  Colors.pinkAccent, // X
  Colors.orangeAccent, // Y
  Colors.redAccent, // Z
];

class RandomColorAvatar extends StatelessWidget {
  final String name;
  final double width;
  final double height;
  final Color? color;

  const RandomColorAvatar({
    super.key,
    required this.name,
    this.width = 50.0,
    this.height = 50.0,
    this.color,
  });

  // Function to get the color based on the first letter of the name
  Color getColorFromName() {
    if (name.isNotEmpty) {
      String firstLetter = name[0].toUpperCase();
      int index = firstLetter.codeUnitAt(0) - 65; // A = 65 in ASCII
      return avatarColors[index % avatarColors.length]; // To handle non-alphabet characters
    }
    return avatarColors[0]; // Default color if the name is empty
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      width: width,
      height: height,
      decoration: BoxDecoration(
        color: color ?? getColorFromName(),
        shape: BoxShape.circle,
      ),
      alignment: Alignment.center,
      child: Text(
        name.isNotEmpty ? name[0].toUpperCase() : '?',
        style: TextStyle(
          color: Colors.white,
          fontSize: min(width, height) / 2, // Text size adjusts with avatar size
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }
}
