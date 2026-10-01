# Coffee Grade Identification (CGI)

**Coffee Grade Identification (CGI)** is an automated coffee bean quality assessment and suggested transactional pricing system developed as a Computer Engineering thesis prototype.

The system is designed for **green Robusta coffee beans** and uses **computer vision and machine learning** to detect visible coffee bean defects. The detected defects are evaluated using a rule-based defect point system to determine the coffee quality grade and calculate a suggested transactional price based on the recorded sample weight.

## Software Workflow

The CGI transaction follows six main steps:

**Prepare → Weight → Capture → Analyze → Results → Receipt**

1. **Prepare** — Checks system readiness and sample preparation requirements.
2. **Weight** — Records and validates the coffee sample weight.
3. **Capture** — Captures the complete sample from **Side A and Side B**.
4. **Analyze** — Processes both images using the computer vision model to detect coffee beans and visible defects.
5. **Results** — Displays the identified grade, detected defects, sample weight, and suggested transactional price.
6. **Receipt** — Generates a printable summary of the completed assessment.

## Machine Learning & Computer Vision

CGI uses **Ultralytics YOLOv8** as its computer vision model for object detection.

The model is trained using annotated images of green Robusta coffee beans. Bounding boxes and class labels are used to teach the model to locate coffee beans and identify visible defect classes within captured images.

The intended processing pipeline is:

`Image Capture → Preprocessing → Bean/Defect Detection → Defect Assessment → Grade Identification → Pricing`

Both sides of the coffee sample are captured before the final assessment to provide additional visual information about defects that may only be visible from one orientation.

### AI and Rule-Based Processing

The system combines **machine learning** with **rule-based processing**:

- **YOLOv8 / Computer Vision** — Detects coffee beans and visible defect classes from the captured images.
- **Defect Point System** — Converts detected defect counts into corresponding defect points.
- **Grade Identification** — Determines whether the sample belongs to **Extra Class, Class I, or Class II** based on the implemented grading criteria.
- **Pricing Logic** — Uses the identified grade, configured reference price, and measured sample weight to calculate a suggested transactional price.

This means the AI component performs the visual detection, while the final grading and pricing decisions are handled by programmed system rules.

## Software Technologies

| Component | Technology |
|---|---|
| User Interface | HTML, CSS, JavaScript |
| Backend | PHP |
| Machine Learning | Python |
| Computer Vision | Ultralytics YOLOv8 |
| Camera Access | MediaDevices API / UVC Camera |
| Data Storage | Database |
| Target Platform | Raspberry Pi OS |

## Current Development

The CGI software is currently under development. The web interface and complete transaction workflow are being developed alongside the computer vision model.

During software development, temporary system values are used for hardware-dependent functions until the camera, weighing scale, trained YOLOv8 model, and thermal printer are fully integrated.

## Project Scope

This repository focuses on the **software implementation** of CGI, including:

- Web-based transaction interface
- Camera and image acquisition
- Weight data integration
- YOLOv8 model integration
- Coffee bean defect detection
- Defect point and grading logic
- Suggested transactional pricing
- Transaction records
- Receipt generation and printing

## Disclaimer

CGI is an academic prototype. The generated quality assessment and suggested transactional price are intended for research and decision-support purposes within the scope of the study.