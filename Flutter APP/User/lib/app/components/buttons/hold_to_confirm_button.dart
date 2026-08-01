import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';

import '../../../core/utils/util_exporter.dart';

class HoldToConfirmButton extends StatefulWidget {
  static const _minHoldDuration = 500;
  static const Duration _minDuration = Duration(milliseconds: _minHoldDuration);

  const HoldToConfirmButton({
    super.key,
    this.child,
    required this.onProgressCompleted, // progress completed
    required this.onApiCall, // Added API call callback
    required this.onProgressChange, // Callback for progress change
    this.duration = const Duration(milliseconds: 1200),
    this.scaleDuration = const Duration(milliseconds: 300),
    this.hapticFeedback = true,
    this.backgroundColor = Colors.black,
    this.fillColor = Colors.white24,
    this.borderRadius = const BorderRadius.all(Radius.circular(50)),
    this.contentPadding = const EdgeInsets.symmetric(
      vertical: 32,
      horizontal: 32,
    ),
    this.border,
    this.zoomScale = true,
  })  : assert(
          duration >= _minDuration,
          'HoldToConfirmButton duration must be at least $_minHoldDuration milliseconds.',
        ),
        assert(
          duration > scaleDuration,
          'HoldToConfirmButton duration must be greater than the scale duration.',
        );

  final Widget? child;
  final Future<void> Function() onApiCall, onProgressCompleted; // Callback for API call
  final Function(double) onProgressChange; // Callback for progress change
  final Duration duration;
  final Duration scaleDuration;
  final bool hapticFeedback;
  final Color backgroundColor;
  final Border? border;
  final Color fillColor;
  final BorderRadius borderRadius;
  final EdgeInsets contentPadding;
  final bool zoomScale;

  @override
  State<HoldToConfirmButton> createState() => _HoldToConfirmButtonState();
}

class _HoldToConfirmButtonState extends State<HoldToConfirmButton> with TickerProviderStateMixin {
  late final AnimationController scaleController;
  late final AnimationController progressController;
  bool isCompleted = false;
  double progress = 0.0;

  @override
  void initState() {
    super.initState();

    scaleController = AnimationController(
      vsync: this,
      duration: widget.scaleDuration,
    );

    progressController = AnimationController(vsync: this, value: 0.03, duration: widget.duration)
      ..addStatusListener(_onCompletedCallback)
      ..addListener(() {
        setState(() {
          progress = progressController.value; // Ensure progress is updated in the widget
        });
        widget.onProgressChange(
          progressController.value,
        ); // Notify about progress change
      });
  }

  void _onCompletedCallback(AnimationStatus status) async {
    if (status == AnimationStatus.completed) {
      if (widget.hapticFeedback) {
        HapticFeedback.mediumImpact();
      }

      setState(() {
        isCompleted = true;
      });

      // try {
      await widget.onApiCall().whenComplete(() async {
        await widget.onProgressCompleted();
      });

      // await Future.delayed(Duration(seconds: 3));
      _reverseAnimation();

      return;
      // } catch (e) {
      //   CustomSnackBar.error(errorList: [
      //     "Action Failed!",
      //   ]);
      // setState(() {
      //   isCompleted = false;
      // });
      // }
    }
  }

  @override
  void dispose() {
    scaleController.dispose();
    progressController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // ignore: unnecessary_null_comparison
    final isDisabled = widget.onApiCall == null;

    return AbsorbPointer(
      absorbing: isDisabled,
      child: AnimatedOpacity(
        opacity: isDisabled ? 0.5 : 1.0,
        duration: const Duration(milliseconds: 200),
        child: GestureDetector(
          onTapDown: (_) => _forwardAnimation(),
          onTapUp: (_) {
            if (isCompleted == false) {
              _reverseAnimation();
            }
          },
          onTapCancel: _reverseAnimation,
          child: Stack(
            alignment: Alignment.center,
            children: [
              ScaleTransition(
                scale: widget.zoomScale
                    ? Tween<double>(begin: 1.0, end: 0.9).animate(
                        CurvedAnimation(
                          parent: scaleController,
                          curve: Curves.easeInOut,
                        ),
                      )
                    : Tween<double>(begin: 1.0, end: 1).animate(
                        CurvedAnimation(
                          parent: scaleController,
                          curve: Curves.easeInOut,
                        ),
                      ),
                child: AnimatedBuilder(
                  animation: progressController,
                  builder: (_, child) {
                    return ShaderMask(
                      blendMode: BlendMode.srcATop,
                      shaderCallback: (rect) {
                        return LinearGradient(
                          colors: [widget.fillColor, Colors.transparent],
                          stops: [
                            progressController.value,
                            progressController.value,
                          ],
                        ).createShader(rect);
                      },
                      child: child,
                    );
                  },
                  child: DecoratedBox(
                    decoration: BoxDecoration(
                      color: widget.backgroundColor,
                      borderRadius: widget.borderRadius,
                      border: widget.border,
                    ),
                    child: Padding(
                      padding: widget.contentPadding,
                      child: widget.child ?? SizedBox(height: 10, width: double.infinity),
                    ),
                  ),
                ),
              ),
              // Updated Text widget to display progress percentage
              // AnimatedSwitcher to handle both progress text and CircularProgressIndicator
              AnimatedSwitcher(
                duration: const Duration(milliseconds: 500),
                transitionBuilder: (Widget child, Animation<double> animation) {
                  return FadeTransition(opacity: animation, child: child);
                },
                child: isCompleted
                    ? CircularProgressIndicator(
                        key: ValueKey<bool>(
                          isCompleted,
                        ), // Key to trigger the switch when completed
                        valueColor: AlwaysStoppedAnimation<Color>(
                          MyColor.getWhiteColor(),
                        ),
                        strokeWidth: 3,
                      )
                    : Text(
                        MyStrings.holdMeToConfirm.tr, // Display percentage
                        key: ValueKey<bool>(
                          !isCompleted,
                        ), // Key to switch back to progress text
                        textAlign: TextAlign.center,
                        style: MyTextStyle.bodyTextStyle1.copyWith(
                          color: (progressController.value * 100) > 40 ? MyColor.getWhiteColor() : MyColor.getBodyTextColor(),
                        ),
                      ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _forwardAnimation() {
    scaleController.forward();
    progressController.forward(from: 0.03); // Start animation from 20%
  }

  void _reverseAnimation() {
    scaleController.reverse();
    progressController.reverse();
    progressController.value = 0.03;
    setState(() {
      isCompleted = false;
    });
  }
}
