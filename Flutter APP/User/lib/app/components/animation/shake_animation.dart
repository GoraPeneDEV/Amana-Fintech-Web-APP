import 'package:flutter/material.dart';

extension ShakeXExtension on Widget {
  Widget shakeX({
    bool condition = true,
    Duration duration = const Duration(milliseconds: 500),
  }) {
    return condition ? _ShakeXAnimation(duration: duration, child: this) : this;
  }
}

class _ShakeXAnimation extends StatefulWidget {
  final Widget child;
  final Duration duration;

  const _ShakeXAnimation({
    required this.child,
    this.duration = const Duration(milliseconds: 500),
  });

  @override
  _ShakeXAnimationState createState() => _ShakeXAnimationState();
}

class _ShakeXAnimationState extends State<_ShakeXAnimation> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _animation;
  bool hasShaken = false; // Track if the shake has already occurred

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(duration: widget.duration, vsync: this);
    _animation = TweenSequence<double>([
      TweenSequenceItem(tween: Tween(begin: 0, end: 6), weight: 1),
      TweenSequenceItem(tween: Tween(begin: 6, end: -6), weight: 2),
      TweenSequenceItem(tween: Tween(begin: -6, end: 6), weight: 2),
      TweenSequenceItem(tween: Tween(begin: 6, end: -6), weight: 2),
      TweenSequenceItem(tween: Tween(begin: -6, end: 6), weight: 2),
      TweenSequenceItem(tween: Tween(begin: 6, end: -6), weight: 2),
      TweenSequenceItem(tween: Tween(begin: -6, end: 0), weight: 1),
    ]).animate(CurvedAnimation(parent: _controller, curve: Curves.easeInOut));

    if (!hasShaken) {
      _controller.forward().whenComplete(
            () => hasShaken = true,
          ); // Mark as shaken after completion
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _animation,
      builder: (context, child) {
        return Transform.translate(
          offset: Offset(_animation.value, 0),
          child: child,
        );
      },
      child: widget.child,
    );
  }
}
