import * as React from "react"
import { cn } from "@/lib/utils"

interface AlertProps extends React.HTMLAttributes<HTMLDivElement> {
  variant?: "default" | "destructive" | "success"
}

const Alert = React.forwardRef<HTMLDivElement, AlertProps>(
  ({ className, variant = "default", ...props }, ref) => {
    return (
      <div
        ref={ref}
        role="alert"
        className={cn(
          "relative w-full rounded-lg border p-4",
          {
            "bg-background text-foreground": variant === "default",
            "border-destructive/50 text-destructive dark:border-destructive": variant === "destructive",
            "border-green-500/50 text-green-700 dark:border-green-500": variant === "success",
          },
          className
        )}
        {...props}
      />
    )
  }
)
Alert.displayName = "Alert"

export { Alert }
