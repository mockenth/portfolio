/* EDIT THIS FILE to customize the portfolio. Replace placeholders and sample items with real work.
   Keep image paths relative to index.html. Never list credentials or achievements you have not earned. */
window.PORTFOLIO_DATA = {
  owner: {
    name: "CHRISTIAN EARL MOCORRO",
    email: "christian.mocorro@bipsu.edu.ph",
    roles: ["Web Development", "Jersey & Apparel Design", "Software Projects", "Creative Digital Art"]
  },

  /* projects in progress and designs of the owner */
  projects: [
    {
      title: "Road Damage Detection System",
      category: "ai",
      type: "AI & Machine Learning",
      subtitle: "Real-time road damage detection.",
      description: "An AI-powered application designed to identify and classify road damages (potholes, cracks) using computer vision.",
      features: [
        "Integrated the YOLO object detection model with a mobile frontend.",
        "Real-time reporting and location tagging for local government units.",
        "Trained on a custom dataset of regional road conditions."
      ],
      tech: ["Python", "YOLOv11s", "Flutter"],
      date: "2024",
      image: "/assets/images/project-in-progress/road-scan.jpeg",
      link: "",
      github: ""
    },
    {
      title: "Kimud Shoal",
      category: "web",
      type: "Web Development",
      subtitle: "Dive trip booking platform.",
      description: "A Tourism Webapp that allows visitors to book a dive trip to Kimud Shoal, a popular diving destination. The app provides information about the site, available packages, and a booking system.",
      features: [
        "Browse available dive packages and schedules.",
        "Secure booking system for visitors.",
        "Information portal for the Kimud Shoal diving site."
      ],
      tech: ["PHP", "MySQL", "HTML", "CSS", "Javascript"],
      date: "2026",
      image: "/assets/images/project-in-progress/kimud-soal.jpg",
      link: "",
      github: ""
    },
    {
      title: "Grandma's Kitchen Recipes",
      category: "academic",
      type: "Web Development",
      subtitle: "Authentic Filipino recipe sharing.",
      description: "A recipe-sharing site for traditional Filipino dishes that usually get passed down by word of mouth. Users can browse by category, search recipes, and view ingredients and step-by-step instructions, with a simple login system for future recipe saving.",
      features: [
        "Search and filter recipes by category.",
        "View detailed ingredients and step-by-step instructions.",
        "User login system for saving favorite recipes."
      ],
      tech: ["PHP", "MySQL", "HTML", "CSS", "Javascript"],
      date: "2024",
      image: "/assets/images/project-in-progress/gkr.png",
      link: "https://grandmaskitchen.freedev.app/",
      github: ""
    }
  ],

  /* published projects and designs of the owner */
  designs: [
    {
      title: "Basketball Jersey Concept",
      category: "jersey",
      caption: "APPAREL / SPORTSWEAR",
      description: "Custom basketball jersey design concept.",
      image: "assets/images/graphic-designs/basketball-jersey-15.jpg"
    },
    {
      title: "Basketball Jersey Concept",
      category: "jersey",
      caption: "APPAREL / SPORTSWEAR",
      description: "Custom basketball jersey design concept.",
      image: "assets/images/graphic-designs/jt-warriors.png"
    },
    {
      title: "Custom Shirt Layout",
      category: "shirt",
      caption: "APPAREL / MERCH",
      description: "Custom shirt layout and design.",
      image: "assets/images/graphic-designs/pe-shirt.png"
    },
    {
      title: "Custom Shirt Layout",
      category: "shirt",
      caption: "APPAREL / MERCH",
      description: "Custom shirt layout and design.",
      image: "assets/images/graphic-designs/cnsat.02.png"
    },
  ],

  /* certifications */
  certificates: [
    {
      title: "Introduction to AI Literacy and Responsible Use",
      issuer: "Mapua University",
      date: "September 14, 2026",
      category: "AI Literacy",
      image: "assets/images/certificates/ai-literacy.png",
      file: "",
      description: "Completed the micro-credentials course on AI Literacy."
    },
    {
      title: "Networking Academy",
      issuer: "Cisco Networking Academy",
      date: "May 18, 2026",
      category: "Networking",
      image: "assets/images/certificates/cisco-networking-academy.png",
      file: "",
      description: "Student level credential for completing the Networking Basics course."
    },
    {
      title: "Networking Basics",
      issuer: "Networking Academy",
      date: "May 18, 2026",
      category: "Networking",
      image: "assets/images/certificates/networking-basics.png",
      file: "",
      description: "Successfully completed the Networking Basics course."
    },
    {
      title: "Thesis preparation with the use of Trending Tools and Industry Practices",
      issuer: "Biliran Province State University | School of Technology and Computer Studies",
      date: "October 6-7, 2025",
      category: "Academic",
      image: "assets/images/certificates/thesis-preparation.png",
      file: "",
      description: "Participated in thesis preparation seminar."
    },
    {
      title: "DICT ICT Proficiency Diagnostic Examination",
      issuer: "DICT",
      date: "November 26, 2025",
      category: "Proficiency",
      image: "assets/images/certificates/dict.jpg",
      file: "https://web.facebook.com/photo/?fbid=836921002315926&set=a.156000637074636",
      description: "Passed the ICT Proficiency Diagnostic Examination."
    }
  ],

  journey: [
    {
      date: "PRESENT",
      title: "Computer Science Student",
      description: "Currently pursuing a Bachelor of Science in Computer Science at Biliran Province State University (Main Campus), focusing on software development and emerging technologies."
    },
    {
      date: "2024-2025",
      title: "Expanded into Software Development",
      description: "Transitioned from design to programming by building foundational web applications and exploring AI/ML concepts, leading to the development of the Road Damage Detection System."
    },
    {
      date: "2023-2024",
      title: "Started Graphic Design Journey",
      description: "Began exploring digital art and layout design, creating custom basketball jerseys, shirt layouts, and branding concepts for local teams and peers."
    },
    {
      date: "2023",
      title: "First Steps into Tech",
      description: "Developed a strong curiosity for technology, building my first basic HTML/CSS and Django websites and learning the fundamentals of programming logic."
    }
  ],

  /* links of social media accounts of the admin */
  socials: [
    { name: "Facebook",  label: "Facebook",  url: "https://www.facebook.com/ea.rl.1481" },
    { name: "Instagram", label: "Instagram", url: "https://www.instagram.com/earllybirdddd?mdxt=MXZjeHN4MXJhaWZxYQ==" },
    { name: "GitHub",    label: "GitHub",    url: "https://github.com/mockenth" },
    { name: "LinkedIn",  label: "LinkedIn",  url: "https://www.linkedin.com/in/earl-mocorro-8a8b4b2b/" },
    { name: "Behance",   label: "Behance",   url: "https://www.behance.net/earlmocorro" }
  ]
};
